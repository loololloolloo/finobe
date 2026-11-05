<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\frontEnd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

final class ManageUsersController extends Controller {
    private const SECTIONS = [
        'overview'=>'Overview', 'account'=>'Account', 'moderation'=>'Moderation',
        'notes'=>'Staff notes', 'economy'=>'Dius & transactions',
        'avatar'=>'Avatar', 'messages'=>'Messages', 'changes'=>'Staff history',
    ];
    private function db() { return DB::connection('finobe'); }
    private function base(Request $request): array {
        $nav=(new frontEnd($request))->getData();
        return ['actor'=>$request->user(),'navbarHtml'=>view('v2/Modules/nav',$nav)->render()];
    }
    public function index(Request $request) {
        $data=$request->validate([
            'q'=>'nullable|string|max:150',
            'page'=>'nullable|integer|min:1|max:100000',
        ]);
        $q=trim($data['q']??'');
        $users=$this->db()->table('users')->select('id','username','email','status','Dius','created','lastlogin')
            ->when($q!=='',fn($query)=>$query->where(function ($sq) use ($q) {
                $sq->where('username','like','%'.addcslashes($q,'%_\\').'%')
                    ->orWhere('email','like','%'.addcslashes($q,'%_\\').'%');
                if(ctype_digit($q)) $sq->orWhere('id',(int)$q);
            }))
            ->orderByDesc('id')->paginate(25)->withQueryString();
        return view('admin.users.index',$this->base($request)+['users'=>$users,'q'=>$q]);
    }
    public function show(Request $request, string $id, string $section='overview') {
        abort_unless(ctype_digit($id) && isset(self::SECTIONS[$section]),404);
        $db=$this->db();
        $subject=User::findOrFail((int)$id);
        $extras=[];
        $extras['bans']=$db->table('bans')->where('username',$subject->username)->orderByDesc('id')->limit(30)->get();
        $extras['warnings']=$db->table('warning')->where('username',$subject->username)->orderByDesc('id')->limit(30)->get();
        $extras['notes']=$db->table('admin_user_notes')->where('user_id',$subject->id)->orderByDesc('id')->paginate(15);
        $extras['transactions']=$db->table('purchases')->where('username',$subject->username)->orderByDesc('id')->limit(50)->get();
        $extras['messages']=$db->table('messages')->where('author',$subject->id)->orWhere('touser',$subject->id)->count();
        $extras['logs']=$db->table('admin_user_log')->where('user_id',$subject->id)->orderByDesc('id')->limit(50)->get();
        $activeBan=$db->table('bans')->where('username',$subject->username)
            ->whereIn('reactivated',[0,'n'])->whereIn('undone',[0,'n'])
            ->where(function ($q) { $q->whereIn('perm',[1,'y'])->orWhere('expire','>',now()); })
            ->orderByDesc('id')->first();
        $extras['activeBan']=$activeBan;
        return view('admin.users.show',$this->base($request)+$extras+[
            'subject'=>$subject,'section'=>$section,'sectionTitle'=>self::SECTIONS[$section],
            'sections'=>self::SECTIONS,
        ]);
    }
    private function audit(int $userId, int $actorId, string $action, string $details): void {
        $this->db()->table('admin_user_log')->insert([
            'user_id'=>$userId,'moderator_id'=>$actorId,'action'=>$action,'details'=>$details,
            'created_at'=>now(),'updated_at'=>now()
        ]);
    }
    public function note(Request $request,string $id) {
        $subject=User::findOrFail((int)$id);
        $data=$request->validate(['body'=>'required|string|min:2|max:5000']);
        $this->db()->transaction(function () use ($subject,$request,$data) {
            $this->db()->table('admin_user_notes')->insert([
                'user_id'=>$subject->id,'moderator_id'=>$request->user()->id,
                'body'=>$data['body'],'created_at'=>now(),'updated_at'=>now()
            ]);
            $this->audit((int)$subject->id,(int)$request->user()->id,'note','Added a staff note');
        });
        return redirect('/admin/users/'.$id.'/notes')->with('admin_success','Private staff note added.');
    }
    public function updateAccount(Request $request,string $id) {
        $subject=User::findOrFail((int)$id);
        $data=$request->validate([
            'email'=>['required','email','max:255',Rule::unique('finobe.users','email')->ignore($subject->id)],
        ]);
        $old=$subject->email; $subject->email=$data['email']; $subject->save();
        $this->audit((int)$subject->id,(int)$request->user()->id,'email','Email updated from '.$old);
        return redirect('/admin/users/'.$id.'/account')->with('admin_success','Account updated.');
    }
    public function changeDius(Request $request,string $id) {
        $subject=User::findOrFail((int)$id);
        $data=$request->validate(['amount'=>'required|integer|between:-100000000,100000000','reason'=>'required|string|min:3|max:500']);
        $amount=(int)$data['amount'];
        abort_if($amount===0,422,'Amount must be nonzero.');
        $this->db()->transaction(function () use ($request,$subject,$amount,$data) {
            $row=$this->db()->table('users')->where('id',$subject->id)->lockForUpdate()->first();
            abort_if((int)$row->Dius+$amount<0,422,'Insufficient Dius.');
            $this->db()->table('users')->where('id',$subject->id)->increment('Dius',$amount);
            $this->db()->table('purchases')->insert([
                'username'=>$subject->username,'assetid'=>0,'serial'=>0,'author'=>$request->user()->id,
                'amount'=>$amount,'type'=>'admin',
                'date'=>now(),'lastchanged'=>now(),
            ]);
            $this->audit((int)$subject->id,(int)$request->user()->id,'dius',$amount.' Dius: '.$data['reason']);
        });
        return redirect('/admin/users/'.$id.'/economy')->with('admin_success','Dius balance updated.');
    }
    public function moderate(Request $request,string $id) {
        $subject=User::findOrFail((int)$id);
        $data=$request->validate([
            'action'=>'required|in:warn,ban,unban',
            'reason'=>'required_unless:action,unban|nullable|string|min:3|max:1000',
            'permanent'=>'nullable|boolean',
            'days'=>'required_if:action,ban|nullable|integer|min:1|max:3650',
        ]);
        $actor=$request->user();
        abort_if((int)$actor->id===(int)$subject->id && $data['action']==='ban',422,'You cannot ban yourself.');
        $db=$this->db();
        $db->transaction(function () use ($request,$subject,$actor,$db,$data) {
            $a=$data['action'];
            if($a==='warn') {
                $db->table('warning')->insert([
                    'username'=>$subject->username,'reason'=>$data['reason'],'moderator'=>$actor->username,
                    'reactivated'=>false,'undone'=>false,'date'=>now(),
                ]);
            } elseif($a==='ban') {
                $permanent=filter_var($data['permanent']??false,FILTER_VALIDATE_BOOLEAN);
                $db->table('bans')->insert([
                    'username'=>$subject->username,'reason'=>$data['reason'],'moderator'=>$actor->username,
                    'reactivated'=>false,'undone'=>false,'perm'=>$permanent,
                    'expire'=>$permanent?null:now()->addDays((int)$data['days']),
                    'date'=>now(),
                ]);
            } else {
                $db->table('bans')->where('username',$subject->username)
                    ->whereIn('reactivated',[0,'n'])->whereIn('undone',[0,'n'])
                    ->update(['undone'=>true,'reactivated'=>true]);
            }
            $this->audit((int)$subject->id,(int)$actor->id,$a,trim((string)($data['reason']??$a)));
        });
        return redirect('/admin/users/'.$id.'/moderation')->with('admin_success','Moderation action recorded.');
    }
}
