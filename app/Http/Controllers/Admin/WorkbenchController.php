<?php
namespace App\Http\Controllers\Admin;

use App\Admin\Access;
use App\Admin\Catalog;
use App\Admin\Workbench;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class WorkbenchController extends Controller
{
    public function index(Request $request) {
        Access::authorize($request->user());
        abort_unless(Schema::connection('finobe')->hasTable('admin_audit'),503,'Run the admin workbench migration first.');
        $actions=array_values(array_filter(Catalog::all(),fn($a)=>in_array($a['group'],Access::scopes($request->user()),true)));
        $navbar=(new \App\Http\Controllers\frontEnd($request))->getData();
        $navbar['data']['admin_panel']=true;
        $navbar['data']['user']['branding']='finobe';
        $navbar['data']['notifications']['ads']=false;
        return view('admin.workbench',[
            'actor'=>$request->user(), 'actions'=>$actions,
            'navbarHtml'=>view('v2/Modules/nav',$navbar)->render(),
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
        Access::authorize($request->user());
        $request->validate(['q'=>'required|string|max:80']);
        $a=Catalog::find('accounts-inspect-account');
        // Staff with any user-oriented scope can resolve a username, without receiving private fields.
        $user=$workbench->target($a,$request->input('q'));
        $scopes=Access::scopes($request->user());
        $history=in_array('Moderation',$scopes,true)?$workbench->run(Catalog::find('moderation-read-moderation-history'),['target'=>(string)$user->id],$request->user()):[];
        return response()->json(['user'=>['id'=>$user->id,'username'=>$user->username,'status'=>$user->status,'pfp'=>$user->pfp,'Dius'=>in_array('Economy',$scopes,true)?$user->Dius:null], 'history'=>$history]);
    }
}
