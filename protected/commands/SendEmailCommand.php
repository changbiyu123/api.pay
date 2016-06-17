<?php

class SendEmailCommand extends CConsoleCommand {

    /**
     * @cron * * * * *
     * every 2 mins
     */
    public function actionMessageQueue() {
        $timeStart = time();    //starting time.
        $duration = 115;           //execution duration allowed.
        $limit = 120;
        $offset = 0;
        $models = MessageQueue::model()->getAllByNotSent($limit, $offset);
        $countSent = 0;
        if (is_array($models) && count($models) > 0) {
            $emailMgr = new EmailManager();
            foreach ($models as $model) {
                $timeNow = time();
                if (($timeNow - $timeStart) > $duration) {
                    break;
                }
                //Send email.
                if ($emailMgr->sendEmailMessageQueue($model)) {
                    $model->success = 1;
                    $model->date_sent = new CDbExpression('NOW()');
                    $countSent++;
                }
                $model->attempts = $model->attempts + 1;
                $model->last_attempt = new CDbExpression('NOW()');

                $model->save();
            }
        }
        //echo $countSent;
        return true;
    }

}

