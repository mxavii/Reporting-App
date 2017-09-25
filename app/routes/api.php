<?php

$app->get('/task/cron/', 'App\Cron\CronJob:running');
$app->get('/activateaccount/{token}', 'App\Controllers\api\UserController:activateAccount')->setName('api.activate');
$app->group('/api', function() use ($app, $container) {
    $app->get('/', 'App\Controllers\api\UserController:index');
    $app->post('/login', 'App\Controllers\api\UserController:login')->setname('api.user.login');
    $app->get('/logout', 'App\Controllers\api\UserController:logout')->setname('api.logout');
    $app->post('/register', 'App\Controllers\api\UserController:register')->setname('api.register');
    $app->post('/forgot-password', 'App\Controllers\api\UserController:recovery')->setName('api.recovery');
    $app->post('/reset', 'App\Controllers\api\UserController:forgotPassword')->setName('api.reset');
    $app->get('/password/reset/{token}', 'App\Controllers\api\UserController:getResetPassword')->setName('api.get.reset');
    $app->post('/password/reset', 'App\Controllers\api\UserController:resetPassword')->setName('api.post.reset');
    // $app->post('/test', 'App\Controllers\api\UserController:changePassword')->setName('api.reset.password');

    $app->group('', function() use ($app, $container) {
        $app->post('/comment', 'App\Controllers\api\CommentController:createComment')->setname('api.post.comment');
        $app->get('/comment/delete/{id}', 'App\Controllers\api\CommentController:deleteComment')->setname('api.delete.comment');
    $app->group('/item', function() use ($app, $container) {
        $app->get('', 'App\Controllers\api\ItemController:all')->setname('api.item.all');
        $app->get('/{id}', 'App\Controllers\api\ItemController:getItemDetail')->setname('api.item.Detail');
        $app->get('/delete/{id}', 'App\Controllers\api\ItemController:deleteItem')->setname('api.item.delete');
        $app->get('/{item}/user', 'App\Controllers\api\ItemController:deleteItemByUser')->setname('api.user.delete.item');
        $app->post('/edit/{id}', 'App\Controllers\api\ItemController:updateItem')->setname('api.item.update');
        $app->post('/upload/{item}', 'App\Controllers\api\ItemController:postImages')->setname('api.item.upload');
        $app->get('/image/{item}', 'App\Controllers\api\ItemController:getImageItem')->setname('api.item.image');
        $app->get('/image/delete/{image}', 'App\Controllers\api\ItemController:deleteImageItem')->setname('api.delete.image');
        $app->post('/create', 'App\Controllers\api\ItemController:createItem')->setname('api.item.create');
        $app->post('/{group}', 'App\Controllers\api\ItemController:createItemUser')->setname('api.item.user.create');
        $app->get('/group/{group}', 'App\Controllers\api\ItemController:getUnreportedGroupItem')->setname('api.group.item');
        $app->get('/group/{group}/all-reported', 'App\Controllers\api\ItemController:getReportedGroupItem')->setname('api.reported.group.item');
        $app->get('/{user}/unreported', 'App\Controllers\api\ItemController:getUnreportedUserItem')->setname('api.unreported.item');
        $app->get('/{user}/reported', 'App\Controllers\api\ItemController:getReportedUserItem')->setname('api.reported.user.item');
        $app->get('/{user}/month', 'App\Controllers\api\ItemController:getReportedByMonth')->setname('api.reported.user.month');
        $app->get('/{user}/year', 'App\Controllers\api\ItemController:getReportedByYear')->setname('api.reported.user.year');
        $app->get('/group/user/reported', 'App\Controllers\api\ItemController:getReportedUserGroupItem')->setname('api.reported.user.group');
        $app->get('/group/user/unreported', 'App\Controllers\api\ItemController:getUnreportedUserGroupItem')->setname('api.unreported.user.group');
        $app->post('/report/{item}', 'App\Controllers\api\ItemController:reportItem')->setname('api.report.item');
        $app->get('/show/{id}', 'App\Controllers\api\ItemController:showItemDetail')->setname('api.item.show');
        $app->get('/comment/{id}', 'App\Controllers\api\CommentController:getItemComment')->setname('api.item.comment');
        $app->post('/add/image', 'App\Controllers\api\ItemController:postImage')->setname('api.item.upload');
    });

    $app->group('/user', function() use ($app, $container) {
        $this->get('/all', 'App\Controllers\api\UserController:index')->setname('api.user.list');
        $this->post('/update/{id}', 'App\Controllers\api\UserController:updateProfile')->setName('api.edit.account');
        $this->post('/password/change', 'App\Controllers\api\UserController:changePassword')->setName('api.change.password');
        $this->get('/detail', 'App\Controllers\api\UserController:detailAccount')->setName('api.detail.account');
        $this->get('/detail/{id}', 'App\Controllers\api\UserController:findUser')->setName('api.detail.user');
        $this->get('/groups', 'App\Controllers\api\GroupController:getGeneralGroup');
        $this->post('/{id}/change-image', 'App\Controllers\api\UserController:postImage')->setname('api.user.image');
        $app->get('/timeline/{id}', 'App\Controllers\api\ItemController:userTimeline')->setname('api.item.timeline');
    });

    $app->group('/group', function() use ($app, $container) {
        $app->post('/create', 'App\Controllers\api\GroupController:add')->setName('api.group.add');
        $app->post('/update', 'App\Controllers\api\GroupController:update')->setName('api.group.update');
        $app->get('/list', 'App\Controllers\api\GroupController:index')->setName('api.group.list');
        $app->get('/enter/{id}', 'App\Controllers\api\GroupController:enterGroup')->setName('api.enter.group');
        $app->get('/find/{id}', 'App\Controllers\api\GroupController:findGroup');
        // $app->get('/delete/{id}', 'App\Controllers\api\GroupController:delete');
        $app->post('/add/user', 'App\Controllers\api\GroupController:setUserGroup')->setName('api.user.add.group');
        $app->post('/set/guardian/{group}/{id}', 'App\Controllers\api\GroupController:setAsGuardian')->setName('api.user.set.guardian');
        $app->get('/detail', 'App\Controllers\api\GroupController:getGroup');
        $app->get('/delete/{id}', 'App\Controllers\api\GroupController:delGroup');
        $app->get('/leave/{id}', 'App\Controllers\api\GroupController:leaveGroup');
        $app->get('/join/{id}', 'App\Controllers\api\GroupController:joinGroup');
        $app->post('/search', 'App\Controllers\api\GroupController:searchGroup')->setName('api.search.group');
        $app->get('/active', 'App\Controllers\api\GroupController:inActive');
        $app->post('/change/photo/{id}', 'App\Controllers\api\GroupController:postImage')->setName('api.change.photo.group');
        $app->get('/PIC', 'App\Controllers\api\GroupController:getPicGroup');
        $app->post('/softdelete/{id}', 'App\Controllers\api\GroupController:setInActive')->setName('api.delete.group');
        $app->post('/restore/{id}', 'App\Controllers\api\GroupController:restore')->setName('api.restore.group');
        $app->get('/Pic', 'App\Controllers\api\GroupController:getPic');
        $app->get('/pics', 'App\Controllers\api\GroupController:getGroupPic');
        $app->get('/member/all', 'App\Controllers\api\GroupController:getAllGroupMember')->setName('api.member.group');
        $app->get('/members', 'App\Controllers\api\GroupController:getGroupMember')->setName('api.member.group');
        $app->get('/pic', 'App\Controllers\api\GroupController:getGroupPic')->setName('api.pic.group');
        $app->post('/pic/create', 'App\Controllers\api\GroupController:createByUser')->setName('pic.create.group');
        $app->get('/{id}/notMember', 'App\Controllers\api\GroupController:getNotMember');
        $app->post('/pic/addusers', 'App\Controllers\api\GroupController:setMemberGroup')->setName('pic.member.group.set');
        $app->post('/upload/image', 'App\Controllers\api\FileSystemController:upload')->setName('api.upload.image');
        $app->get('/{id}/member', 'App\Controllers\api\GroupController:getAllUserGroup');
        $app->post('/pic/set/status/{id}', 'App\Controllers\api\GroupController:setAsPic');
        $app->get('/delete/member/{id}/{group}', 'App\Controllers\api\GroupController:deleteUser');
        $app->post('/pic/set/member/{id}', 'App\Controllers\api\GroupController:setAsMember');
        // $app->get('/user/join', 'App\Controllers\api\GroupController:getUserGroup');
        // $app->get('/items/group/{group}', 'App\Controllers\web\ItemController:getUserInGroupItem')->setName('api.group.item');
    });

    $app->group('/guard', function() use ($app, $container) {
        $app->get('/all', 'App\Controllers\api\GuardController:getAll')->setName('api.guard');
        $app->post('/create', 'App\Controllers\api\GuardController:createGuardian')->setName('api.guard.add');
        // $app->get('/delete/{id}', 'App\Controllers\api\GuardController:deleteGuardian')->setName('api.guard.delete');
        $app->get('/show/user', 'App\Controllers\api\GuardController:getUserByGuard')->setName('api.guard.show.user');
        $app->get('/show/{id}', 'App\Controllers\api\GuardController:getGuardByUser')->setName('api.guard.show');
        $app->get('/user', 'App\Controllers\api\GuardController:getUser')->setName('api.guard.get.user');
        $app->get('/timeline/{id}', 'App\Controllers\api\ItemController:guardTimeline')->setname('api.guard.timeline');
        $app->get('/delete/{id}', 'App\Controllers\api\GuardController:deleteGuardian')->setName('api.guard.delete');
        $app->get('/delete/user/{id}', 'App\Controllers\api\GuardController:deleteUser')->setName('api.guard.delete.user');});

    $app->group('/request', function() use ($app, $container) {
       $app->post('/guard/{guard}', 'App\Controllers\api\RequestController:createUserToGuard')->setName('api.request.guard');
       $app->post('/group', 'App\Controllers\api\RequestController:createUserToGroup')->setName('api.request.group');
       $app->post('/user/{user}', 'App\Controllers\api\RequestController:createGuardToUser')->setName('api.request.user');
       $app->get('/user', 'App\Controllers\api\RequestController:userRequest')->setName('api.notif.user');
       $app->get('/guard', 'App\Controllers\api\RequestController:guardRequest')->setName('api.notif.guard');
       $app->get('/group', 'App\Controllers\api\RequestController:groupRequest')->setName('api.notif.group');
       $app->get('/group/all', 'App\Controllers\api\RequestController:allGroupRequest')->setName('api.notif.all.group');
       $app->get('/all', 'App\Controllers\api\RequestController:allRequest')->setName('api.notif.all');
       $app->get('/delete/{id}', 'App\Controllers\api\RequestController:deleteRequest')->setName('api.request.delete');
       $app->post('/guardian/{guard}', 'App\Controllers\api\RequestController:requestByUser')->setname('api.fellow.request');
       $app->post('/fellow/{user}', 'App\Controllers\api\RequestController:requestByGuard')->setname('api.guardian.request');
   });
})->add(new \App\Middlewares\AuthToken($container));
});
