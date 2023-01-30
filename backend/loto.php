<?php
include_once("api.php");
include_once("database.php");

class Loto
{
    public $pdo;
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function init_loto($by, $lotoLabel, $win_price, $play_price)
    {
        $this->pdo->prepare("INSERT INTO loto (`by`, loto_label, play_price, win_price, launched_at) VALUES (?, ?, ?, ?, ?)")->execute([$by, $lotoLabel, $play_price, $win_price, time()]);
        return 0;
    }

    function get_current_loto()
    {
        $this->pdo->query("SELECT * FROM loto LIMIT 1")->fetch();
        return 0;
    }

    function declare_winner($lotoID)
    {
        $loto = $this->get_loto($lotoID);
        $participants = array_values(explode(",", $loto['participants']));
        array_pop($participants);
        $winner = $participants[random_int(0, count($participants) - 1)];
        return $winner;
    }

    function assign_winner($winnerID, $lotoID)
    {
        $req = $this->pdo->prepare("UPDATE loto SET won_by=? WHERE id=?");
        $req->execute([$winnerID, $lotoID]);
    }

    function get_play_price($user_id, $amount)
    {
        $this->pdo->prepare("UPDATE loto SET bbl=? WHERE id=?")->execute([$amount, $user_id]);
        return 0;
    }

    function get_lotos()
    {
        $req = $this->pdo->query("SELECT * FROM loto");
        $res = $req->fetchAll();
        return $res;
    }

    function get_loto($lotoID)
    {
        $req = $this->pdo->query("SELECT * FROM loto WHERE id=$lotoID LIMIT 1");
        $res = $req->fetch();
        return $res;
    }

    function winEmail($email, $price)
    {
        $message = "<html><body><p>Bravo à vous !<br><br>Vous venez de gagner {$price} BBL ! Connectez-vous à votre compte pour vous en servir.<br><br>Si vous n'êtes pas à l'origine de cet email, ignorez-le.</p></body></html>";
        $headers = "CC: {$email}\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
        $result = mail($email, "Événement {$_SERVER['SERVER_NAME']}", $message, $headers);
        if (!$result) {
            return ['invalid_email' => "L'adresse mail est invalide, aucune confirmation d'IP ne peut être effectuée. Merci de contacter un administrateur !"];
        }
        return true;
    }

    function credit($winner, $loto)
    {
        // var_dump([$loto['win_price'] + $winner['bbl'], $winner['ID']]);
        $this->pdo->prepare("UPDATE loto SET won_by=? WHERE id=?")->execute([$winner['ID'], $loto['id']]);
        $this->pdo->prepare("UPDATE users SET bbl=? WHERE id=?")->execute([$loto['win_price'] + $winner['bbl'], $winner['ID']]);
        $result = $this->winEmail($winner['email'], $loto['win_price']);
        return $result;
    }

    function cancel($lotoID)
    {
        $this->pdo->prepare("UPDATE loto SET won_by=? WHERE id=?")->execute(["canceled", $lotoID]);
    }

    function participate($lotoID, $memberID)
    {
        $loto = $this->get_loto($lotoID);
        $actualBBL = $this->pdo->query("SELECT bbl FROM users WHERE id=$memberID")->fetch();
        if (!empty($actualBBL)) {

            if ($actualBBL['bbl'] - $loto['play_price'] < 0) {
                return ['not_enough_bbl' => "Vous n'avez pas suffisement de BBL pour participer à cet événement !"];
            }
            $this->pdo->prepare("UPDATE loto SET participants=? WHERE id=?")->execute([$loto['participants'] . $memberID . ",", $lotoID]);
            $this->pdo->prepare("UPDATE users SET bbl=? WHERE id=?")->execute([$actualBBL['bbl'] - $loto['play_price'], $memberID]);
            return true;
        }
        return ['internal_error' => "Une erreur s'est produite, déconnecté(e) puis reconnecté(e)-vous et revenez sur cette page pour résoudre le problème !"];
    }
}

$loto = new Loto($pdo);
