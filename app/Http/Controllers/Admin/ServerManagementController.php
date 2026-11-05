<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\frontEnd;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class ServerManagementController extends Controller {
    private function base(Request $request): array {
        $data=(new frontEnd($request))->getData();
        return ['actor'=>$request->user(),'navbarHtml'=>view('v2/Modules/nav',$data)->render()];
    }
    public function index(Request $request) {
        $servers=Server::query()->orderByDesc('id')->paginate(30);
        $summary=['total'=>Server::count(),'active'=>Server::where('status','active')->count()];
        return view('admin.servers',$this->base($request)+['servers'=>$servers,'summary'=>$summary]);
    }
    public function store(Request $request) {
        $input=$request->validate([
            'ip'=>'required|ip',
            'port'=>'required|integer|between:1,65535',
            'placeid'=>'required|integer|min:1',
            'jobId'=>'required|string|min:6|max:255|unique:finobe.servers,jobId',
            'status'=>'required|string|in:active,starting,stopped',
        ]);
        Server::create($input+['players'=>[]]);
        return redirect('/admin/servers')->with('admin_success','Game server added.');
    }
    public function destroy(string $id) {
        abort_unless(ctype_digit($id),404);
        Server::where('id',(int)$id)->firstOrFail()->delete();
        return redirect('/admin/servers')->with('admin_success','Server removed.');
    }
}
