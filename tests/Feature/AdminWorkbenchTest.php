<?php
namespace Tests\Feature;

use App\Admin\Catalog;
use App\Admin\Workbench;
use App\Admin\Access;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminWorkbenchTest extends TestCase
{
    private User $admin;
    private User $member;
    protected function setUp(): void {
        parent::setUp();
        config(['database.connections.finobe'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'','foreign_key_constraints'=>true], 'app.key'=>'base64:'.base64_encode(str_repeat('t',32)), 'session.driver'=>'array','cache.default'=>'array','admin.owner_ids'=>[1]]);
        DB::purge('finobe');
        Artisan::call('migrate',['--database'=>'finobe','--force'=>true]);
        $db=DB::connection('finobe');
        foreach([[1,'Moderator','admin'],[2,'Builder','member'],[3,'Helper','member']] as [$id,$name,$status])$db->table('users')->insert(['id'=>$id,'username'=>$name,'email'=>strtolower($name).'@example.test','password'=>bcrypt('test-only-password'),'status'=>$status,'verified'=>'y','Dius'=>100,'avatar'=>json_encode([['equippedGearVersionIds'=>[108,111],'bodyColors'=>[]]]),'created'=>now(),'lastlogin'=>now(),'friends'=>'[]','inventory'=>'[]','badges'=>'[]']);
        foreach([1,2,3,4,8,9,11,12,13,18,19] as $type)$db->table('assets')->insert(['id'=>100+$type,'asset_type'=>(string)$type,'title'=>'Test asset','author'=>2,'file'=>'admin-workbench-test-'.$type.'.bin','visibility'=>'r','additional'=>json_encode(['media'=>[],'price'=>0,'onSale'=>false])]);
        $db->table('bans')->insert(['id'=>1,'username'=>'Nobody','reason'=>'A historical case','moderator'=>'Moderator','reactivated'=>true,'undone'=>false,'perm'=>false,'expire'=>now()->subDay()]);
        $db->table('warning')->insert(['id'=>1,'username'=>'Nobody','reason'=>'A historical case','moderator'=>'Moderator','reactivated'=>true,'undone'=>false]);
        $db->table('forum_threads')->insert(['id'=>1,'category'=>1,'author'=>2,'title'=>'A thread','comment'=>'Hello']);
        $db->table('forum_replies')->insert(['id'=>1,'toid'=>1,'author'=>2,'comment'=>'A reply']);
        $db->table('messages')->insert(['id'=>1,'author'=>1,'touser'=>2,'subject'=>'Hello','message'=>'Staff message']);
        $db->table('pms')->insert(['id'=>1,'touser'=>2,'owner'=>1,'message'=>'Notification']);
        $db->table('purchases')->insert(['id'=>1,'username'=>'Builder','assetid'=>108,'serial'=>0,'author'=>1,'amount'=>0,'type'=>'1']);
        $db->table('invitekeys')->insert(['id'=>1,'author'=>2,'IID'=>'TESTKEY','used'=>false]);
        $db->table('videos')->insert(['id'=>1,'author'=>2,'title'=>'Test video','filename'=>'test.mp4','thumbnail'=>'test.png']);
        $db->table('announcements')->insert(['id'=>1,'author'=>1,'message'=>'Test announcement','expire'=>now()->addDay(),'color'=>'info']);
        $db->table('failed_jobs')->insert(['id'=>1,'uuid'=>(string)Str::uuid(),'connection'=>'database','queue'=>'default','payload'=>'private payload','exception'=>'private exception']);
        $this->admin=User::find(1);$this->member=User::find(2);
    }
    private function runControl(string $id,array $data=[]):array {
        return app(Workbench::class)->run(Catalog::find($id),array_merge(['target'=>'2','reason'=>'Verified test case','request_id'=>(string)Str::uuid(),'confirm'=>true],$data),$this->admin);
    }
    public function test_nonstaff_cannot_open_or_execute_admin_controls():void {
        $this->get('/admin')->assertForbidden();
        $this->actingAs($this->member)->postJson('/admin/controls/execute',['action'=>'moderation-issue-a-warning','target'=>'2','reason'=>'A reason'])->assertForbidden();
    }
    public function test_panel_uses_the_shared_finobe_navbar_without_custom_branding_or_sidebar_counts():void {
        $this->actingAs($this->admin)->get('/admin')->assertOk()->assertSee('finnobe3logo.png')->assertDontSee('finobe-wordmark.png')->assertDontSee('available controls')->assertSee('Moderation history')->assertSee('catalog_container')->assertSee('id="admin-menu"', false)->assertSee('id="admin-workspace" hidden', false)->assertDontSee('<aside class="sidebar">', false);
    }
    public function test_warning_is_saved_and_requires_acknowledgement_on_the_site():void {
        $this->runControl('moderation-issue-a-warning');
        Route::middleware('web')->get('/policy-test',fn()=>response('Allowed'));
        $this->actingAs($this->member)->get('/policy-test')->assertForbidden()->assertSee('Verified test case')->assertSee('Reactivate my account');
        $this->post('/moderation/acknowledge',['acknowledged'=>1])->assertRedirect('/');
        $this->get('/policy-test')->assertOk();
    }
    public function test_ban_blocks_requests_and_lifting_it_restores_access():void {
        $this->runControl('moderation-ban-for-3-days');
        Route::middleware('web')->get('/policy-test',fn()=>response('Allowed'));
        $this->actingAs($this->member)->getJson('/policy-test')->assertForbidden();
        $this->runControl('moderation-lift-active-bans');
        $this->get('/policy-test')->assertOk();
    }
    public function test_mutations_validate_reason_and_confirmation():void {
        $this->actingAs($this->admin)->postJson('/admin/controls/execute',['action'=>'moderation-ban-for-1-day','target'=>'2','request_id'=>(string)Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors(['reason','confirm']);
        $this->assertEquals(1,DB::connection('finobe')->table('bans')->count());
    }
    public function test_duplicate_request_does_not_double_credit():void {
        $id=(string)Str::uuid();$first=$this->runControl('economy-credit-dius',['value'=>50,'request_id'=>$id]);$again=$this->runControl('economy-credit-dius',['value'=>50,'request_id'=>$id]);
        $this->assertEquals(150,User::find(2)->Dius);$this->assertSame($first['audit_id'],$again['audit_id']);
        $this->assertEquals(1,DB::connection('finobe')->table('admin_audit')->count());
    }
    public function test_delegated_staff_cannot_escape_their_scope():void {
        $this->runControl('staff-grant-access',['target'=>'3','value'=>'Forum']);
        $this->actingAs(User::find(3))->postJson('/admin/controls/execute',['action'=>'economy-credit-dius','target'=>'2','value'=>100,'reason'=>'No permission','request_id'=>(string)Str::uuid(),'confirm'=>true])->assertForbidden();
        $this->postJson('/admin/controls/execute',['action'=>'forum-lock-thread','target'=>'1','reason'=>'Discussion concluded','request_id'=>(string)Str::uuid()])->assertOk();
        $this->assertTrue((bool)DB::connection('finobe')->table('forum_threads')->value('locked'));
    }
    public function test_revoking_permissions_takes_effect_immediately():void {
        $this->runControl('staff-grant-access',['target'=>'3','value'=>'Forum']);$this->runControl('staff-revoke-all-delegated-access',['target'=>'3']);
        $this->actingAs(User::find(3))->get('/admin')->assertForbidden();
    }
    public function test_account_inspection_does_not_disclose_credentials():void {
        $result=$this->runControl('accounts-inspect-account');
        $this->assertArrayNotHasKey('password',$result['rows'][0]);$this->assertArrayNotHasKey('token',$result['rows'][0]);
    }
    public function test_avatar_controls_preserve_the_rest_of_the_avatar():void {
        $data=User::find(2)->avatar;
        $this->assertEquals([108,111],$data[0]['equippedGearVersionIds']);
        $this->runControl('avatar-unequip-hats');$this->assertEquals([111],User::find(2)->avatar[0]['equippedGearVersionIds']);
    }
    public function test_generic_asset_controls_work_across_types():void {
        $this->actingAs($this->admin)->postJson('/admin/controls/execute',['action'=>'catalog-edit-title','target'=>'101','value'=>'Reviewed image','reason'=>'Correct asset title','request_id'=>(string)Str::uuid()])->assertOk();
        $this->assertSame('Reviewed image',DB::connection('finobe')->table('assets')->where('id',101)->value('title'));
        $this->postJson('/admin/controls/execute',['action'=>'catalog-edit-title','target'=>'999999','value'=>'Missing','reason'=>'Missing asset test','request_id'=>(string)Str::uuid()])->assertUnprocessable();
    }
    public function test_restrictions_and_site_pauses_block_real_feature_routes():void {
        Route::middleware('web')->post('/api/character',fn()=>response('Avatar changed'));
        $this->runControl('access-restrictions-block-avatar-changes');
        $this->actingAs($this->member)->postJson('/api/character')->assertForbidden();
        $this->runControl('access-restrictions-restore-avatar-changes');$this->post('/api/character')->assertOk();
        $this->runControl('site-settings-pause-avatar-changes');$this->postJson('/api/character')->assertStatus(503);
        $this->runControl('site-settings-resume-avatar-changes');$this->post('/api/character')->assertOk();
    }
    public function test_removed_presets_are_unavailable_in_the_api():void {
        $this->actingAs($this->admin)->postJson('/admin/controls/execute',['action'=>'fun-give-title-certified-brick','target'=>'2','reason'=>'Removed action','request_id'=>(string)Str::uuid()])->assertNotFound();
        $this->assertFalse(collect(Catalog::all())->contains('group','Fun'));
        $this->assertLessThan(120,count(Catalog::all()));
    }
    public function test_user_directory_search_and_detail_routes():void {
        $this->actingAs($this->admin)->get('/admin/users?q=Builder')->assertOk()->assertSee('Builder')->assertSee('/admin/users/2')->assertDontSee('Helper');
        $this->get('/admin/users?q=2')->assertOk()->assertSee('/admin/users/2');
        $this->get('/admin/users/2')->assertOk()->assertSee('Manage Builder')->assertSee('Last login')->assertSee('Moderation history')->assertSee('"userId":2',false)->assertDontSee('catalog-edit-title');
        $this->get('/admin/users/999999')->assertNotFound();
        $this->get('/admin/users/not-a-number')->assertNotFound();
    }
    public function test_user_management_requires_a_user_oriented_permission():void {
        $this->get('/admin/users')->assertForbidden();
        $this->actingAs($this->member)->get('/admin/users/1')->assertForbidden();
        $this->runControl('staff-grant-access',['target'=>'3','value'=>'Blog']);
        $this->actingAs(User::find(3))->get('/admin/users')->assertForbidden();
        $this->getJson('/admin/controls/user?q=Builder')->assertForbidden();
    }
    public function test_user_page_filters_actions_and_moderation_history_by_scope():void {
        $this->runControl('staff-grant-access',['target'=>'3','value'=>'Economy']);
        $this->actingAs(User::find(3))->get('/admin/users/2')->assertOk()->assertSee('economy-credit-dius')->assertDontSee('"id":"moderation-issue-a-warning"',false);
        $this->getJson('/admin/controls/user?q=Builder')->assertOk()->assertJsonPath('history',[])->assertJsonPath('user.id',2);
        $this->postJson('/admin/controls/execute',['action'=>'moderation-issue-a-warning','target'=>'2','reason'=>'Outside scope','request_id'=>(string)Str::uuid()])->assertForbidden();
    }
    public function test_staff_scope_selector_rejects_unknown_permissions():void {
        $this->actingAs($this->admin)->postJson('/admin/controls/execute',['action'=>'staff-grant-access','target'=>'3','value'=>'Fun','reason'=>'Invalid scope','request_id'=>(string)Str::uuid(),'confirm'=>true])->assertUnprocessable();
    }
    public function test_all_catalog_controls_execute_against_the_schema():void {
        // Exercise every registered control against real migrated tables. Each is isolated,
        // so test presets cannot hide missing handlers, invalid columns, or malformed inputs.
        $seen=[];$failures=[];$db=DB::connection('finobe');
        foreach(Catalog::all() as $action) {
            $this->assertNotContains($action['id'],$seen);$seen[]=$action['id'];
            $db->beginTransaction();
            try {
                $target=$action['target']==='user'?'2':(string)(isset($action['asset_type'])?100+$action['asset_type']:($action['target']==='assets'?101:1));
                if(in_array($action['kind'],['asset_approve','asset_deny'])) {
                    $target='101';$folder=public_path('dynamic/reviewing');if(!is_dir($folder))mkdir($folder,0777,true);file_put_contents($folder.'/admin-workbench-test-1.bin','fixture');config(['admin.cdn_path'=>sys_get_temp_dir().'/finobe-review-test']);
                }
                $rule=$action['input']??'';$value='A useful edited value';
                if(str_contains($rule,'integer'))$value=5;
                if(str_starts_with($rule,'email'))$value='changed@example.test';
                if(str_starts_with($rule,'date'))$value=now()->addDays(2)->format('Y-m-d H:i:s');
                if(str_starts_with($rule,'url'))$value='https://example.test/image.png';
                if(str_starts_with($rule,'in:'))$value=explode(',',substr($rule,3))[0];
                if(($action['field']??'')==='username')$value='NewBuilder';
                if(($action['field']??'')==='pfp')$value='avatar/test.png';
                if($action['kind']==='inventory_grant')$value=119;
                if($action['kind']==='inventory_revoke')$value=108;
                $result=app(Workbench::class)->run($action,['target'=>$target,'value'=>$value,'reason'=>'Catalog integration test','request_id'=>(string)Str::uuid(),'confirm'=>true],$this->admin);
                $this->assertIsArray($result);
            } catch(\Throwable $e) {$failures[]=$action['id'].': '.$e->getMessage();}
            finally {
                $db->rollBack();
                foreach([public_path('dynamic/reviewing/admin-workbench-test-1.bin'),public_path('dynamic/denied/admin-workbench-test-1.bin'),sys_get_temp_dir().'/finobe-review-test/assets/admin-workbench-test-1.bin'] as $file)if(is_file($file))unlink($file);
            }
        }
        $this->assertCount(108,$seen);$this->assertSame([],$failures,implode("\n",$failures));
    }
}
