<?php
class TestCommand extends CConsoleCommand{
    /**
     * @cron * * * * *
     */
    public function actionExample1(){
            $msgqueue=new MessageQueue();
            $msgqueue->from_name='伴客旅行';
            $msgqueue->from_email='norely@guidesky.com';
            $msgqueue->to_email='qin560.user1@gmail.com';
            $msgqueue->subject='测试'.time();
            $msgqueue->message="这是测试电邮";
            $msgqueue->max_attempts=3;
            $msgqueue->save();
            return 1;
    }
}
