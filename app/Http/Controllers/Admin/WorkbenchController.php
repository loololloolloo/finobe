<?php
namespace App\Http\Controllers\Admin;

use App\Admin\Access;
use App\Admin\Catalog;
use App\Admin\Workbench;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class WorkbenchController extends Controller
{
    private function authorizeUsers(Request $request): void {
        Access::authorize($request->user());
        abort_unless(array_intersect(Access::scopes($request->user()), ['Accounts','Moderation','Access restrictions','Economy','Avatar','Messages','Staff','Audit']),403,'User management access required.');
    }
    private function navbar(Request $request): string {
        $navbar=(new \App\Http\Controllers\frontEnd($request))->getData();
        $navbar['data']['admin_panel']=true;
        $navbar['data']['user']['branding']='finobe';
        $navbar['data']['notifications']['ads']=false;
        return view('v2/Modules/nav',$navbar)->render();
    }
    public function users(Request $request) {
        $this->authorizeUsers($request);
        $request->validate(['q'=>'nullable|string|max:80']);
        $q=trim((string)$request->input('q',''));
        $users=DB::connection('finobe')->table('users')->select('id','username','status','created','lastlogin');
        if($q!=='')$users->where(function($query)use($q){
            $query->whereRaw("username LIKE ? ESCAPE '!'",['%'.str_replace(['!','%','_'],['!!','!%','!_'],$q).'%']);
            if(ctype_digit($q))$query->orWhere('id',(int)$q);
        });
        return view('admin.users',['actor'=>$request->user(),'navbarHtml'=>$this->navbar($request),'users'=>$users->orderByDesc('id')->paginate(30)->withQueryString(),'query'=>$q]);
    }
    public function manageUser(Request $request, string $id) {
        $this->authorizeUsers($request);
        abort_unless(ctype_digit($id),404,'User not found.');
        $subject=DB::connection('finobe')->table('users')->select('id','username','status','blurb','created','lastlogin')->where('id',$id)->first();
        abort_unless($subject,404,'User not found.');
        $actions=array_values(array_filter(Catalog::all(),fn($a)=>$a['target']==='user' && in_array($a['group'],Access::scopes($request->user()),true)));
        return view('admin.workbench',['actor'=>$request->user(),'actions'=>$actions,'navbarHtml'=>$this->navbar($request),'managedUser'=>$subject]);
    }
    public function index(Request $request) {
        Access::authorize($request->user());
        abort_unless(Schema::connection('finobe')->hasTable('admin_audit'),503,'Run the admin workbench migration first.');
        $actions=array_values(array_filter(Catalog::all(),fn($a)=>in_array($a['group'],Access::scopes($request->user()),true)));
        return view('admin.workbench',[
            'actor'=>$request->user(), 'actions'=>$actions,
            'navbarHtml'=>$this->navbar($request),
        ]);
    }
    public function acknowledge(Request $request) {
        abort_unless($request->user(),403);
        $request->validate(['acknowledged'=>'required|accepted']);
        $db=DB::connection('finobe');
        $db->table('warning')->where('username',$request->user()->username)
            ->update(['reactivated'=>Workbench::flag('warning','reactivated',true)]);
        return redirect('/');
    }
    public function execute(Request $request,Workbench $workbench) {
        Access::authorize($request->user());
        $request->validate(['action'=>'required|string|max:160']);
        $result=$workbench->run(Catalog::find($request->input('action')),$request->all(),$request->user());
        return response()->json($result);
    }
    public function user(Request $request,Workbench $workbench) {
        $this->authorizeUsers($request);
        $request->validate(['q'=>'required|string|max:80']);
        $a=Catalog::find('accounts-inspect-account');
        // Staff with any user-oriented scope can resolve a username, without receiving private fields.
        $user=$workbench->target($a,$request->input('q'));
        $scopes=Access::scopes($request->user());
        $history=in_array('Moderation',$scopes,true)?$workbench->run(Catalog::find('moderation-read-moderation-history'),['target'=>(string)$user->id],$request->user()):[];
        return response()->json(['user'=>['id'=>$user->id,'username'=>$user->username,'status'=>$user->status,'pfp'=>$user->pfp,'blurb'=>$user->blurb,'Dius'=>in_array('Economy',$scopes,true)?$user->Dius:null], 'history'=>$history]);
    }
}
