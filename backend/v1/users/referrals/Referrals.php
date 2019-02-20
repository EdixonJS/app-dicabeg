<?php

require_once 'ReferralsQuerys.php';

class Referrals
{
    public static function getReferralsAlls()
    {
        $arrayReferrals = ReferralsQuerys::selectAlls();
        for ($i = 0; $i < count($arrayReferrals); $i++) {
            $referred = $arrayReferrals[$i];
            $referredData = self::getReferredData($referred);
            $arrayReferralsData[] = $referredData;
        }
        JsonResponse::read('referrals', $arrayReferralsData);
    }

    public static function getReferredById()
    {
        $arrayReferrals = ReferralsQuerys::selectAlls();
        $referred = self::extractReferred($arrayReferrals);
        $referredData = self::getReferredData($referred);
        JsonResponse::read('referred', $referredData);
    }

    public static function createReferred($referred_id)
    {
        $exitCode = isset($_REQUEST['invite_code']);
        if ($exitCode) {
            $user = UsersQuerys::select('invite_code');
            if ($user) {
                $_GET['id'] = $user->user_id; // id del usuario que otorgo el codigo de invitacion
                $arrayReferrals = ReferralsQuerys::selectAlls(false);
                $arrayReferrals[] = (object)[
                    'referred_id' => $referred_id,
                    'create_date' => date('Y-m-d h:i')
                ];
                $jsonReferrals = json_encode($arrayReferrals);

                ($arrayReferrals) ?
                    ReferralsQuerys::update($jsonReferrals) :
                    ReferralsQuerys::insert(null);

                return 'added as an referred';

            } else throw new Exception('invite code incorrect', 400);
        } else return null;
    }

    public static function removeReferred()
    {
        $arrayReferrals = ReferralsQuerys::selectAlls();
        self::extractReferred($arrayReferrals, $i);
        unset($arrayReferrals[$i]);

        $jsonReferrals = json_encode($arrayReferrals);
        ReferralsQuerys::update($jsonReferrals);
        JsonResponse::removed();
    }

    public static function extractReferred($arrayReferrals, &$index = null)
    {
        for ($i = 0; $i < count($arrayReferrals); $i++) {
            $referred = $arrayReferrals[$i];
            if ($referred->referred_id === $_GET['id_2']) {
                $index = $i;
                return $referred;
            } else continue;
        }
        throw new Exception('not found resourse', 404);
    }

    public static function getReferredData($referred)
    {
        $_GET['id'] = $referred->referred_id;
        $user = UsersQuerys::selectById('user_id, email, username, image, phone');
        $user->create_date = $referred->create_date;
        return $user;
    }
}
