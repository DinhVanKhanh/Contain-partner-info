<?php
    require_once __DIR__ . '/../model/M_mail.class.php';
    require_once __DIR__ . '/../view/V_mail.class.php';
    require_once __DIR__ . '/../../libs/mailer.class.php';

    $model = new M_mail;
    $view  = new V_mail;

    switch ( $_POST['action'] ) {
        case 'loadList':
            // Sample
            $conn = new Database;
            $stmt = $conn->conn->prepare( 'SELECT SampleId, SampleName FROM infoseminar_sample' );
            $stmt->execute();
            $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = $model->getList();
            echo json_encode( $view->loadList( $result, $sample ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['EmailId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["EmailId"] ) );
            echo json_encode( $result );
            break;

        case 'edit':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'id'             => intval( $_POST['id'] ),
                'FromMail'       => $_POST['FromMail'],
                'FromName'       => $_POST['FromName'],
                'MailTest'       => $_POST['MailTest'],
                'EncriptionType' => $_POST['EncriptionType'],
                'Host'           => $_POST['Host'],
                'Port'           => $_POST['Port'],
                'Username'       => $_POST['Username'],
                'Password'       => $_POST['Password'],
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'testMail':
            // Get multi email
            $conn = new Database;
            $stmt = $conn->conn->prepare( 'SELECT SampleEmail FROM infoseminar_sample WHERE TypesId = 2' );
            $stmt->execute();
            $multiEmail = $stmt->fetch(PDO::FETCH_ASSOC)["SampleEmail"];

            $err = sendmail1(
                $_POST['Host'],
                $_POST['Port'],
                $_POST['EncriptionType'],
                (int) $_POST['checkSmtp'],
                $_POST['Username'],
                $_POST['Password'],
                $_POST['FromMail'],
                $_POST['FromName'],
                $_POST['MailTest'],
                'テスト用の送信',
                'テストメールの送信に成功しました。',
                $multiEmail
            );
            $result['errMsg'] = $err;
            echo json_encode($result);
            break;
    }
?>