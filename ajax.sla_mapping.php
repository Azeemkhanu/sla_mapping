<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// echo 'test';
// exit;

include_once "../../../../tempelate/include_files/track_session.php";

//added by anum.rafaqat 18-oct-2022
//including class to generate trouble ticket...
include_once '/var/www/html/ncrm/views/crmViews/nayatelCrm/include_files/troubleTicketTransactions_rapido.php';
// updated by anum,rafaqat 09-DEC-2022
include_once '../include_files/sendMailGeneric.php';
// updated by anum,rafaqat 09-DEC-2022


include_once '../include_files/connectDb.php';



// updated by anum,rafaqat 09-DEC-2022
$mailSender = new mailSender();
// updated by anum,rafaqat 09-DEC-2022


//object of trouble tikcet class...
$newttObj = new troubleTicketTransaction();

$lobjcon = new DbClass1();
$data_type = $_POST['data_type'];

// $operator= $_SESSION['logincrmid'];
// $dept= $_SESSION['subDept'];

// added by azeem for binding queries
include_once "/var/www/html/ncrm/views/crmViews/nayatelCrm/include_files/DbClassBind.php";
$dbObject = new DbClass();

// echo $operator;
// echo $dept;
// exit;
//print_r($_SESSION);

if ($data_type == 'GET_USERID_FOR_TOWERS') {
    $SLA_name = $_POST['sla'];

    if ($SLA_name == 'Telenor') {
        // for cell sites
        $query = "SELECT  USERID FROM mbluser where userid like '%tptower%' and USERID not in (select USERID from FTTT_SLA_MAPPING where fttt_sla=:slaName)order by USERID";
        // $res = $lobjcon->Get_Array($query);
        $params = [
            ':slaName' => $SLA_name
        ];

        // Execute the query for cell sites
        $res = $dbObject->execSelect($query, $params);
        $towers = '<option value="">-- Please Select --</option>';
        for ($i = 0; $i < count($res); $i++) {
            $towers .= '<option value="' . $res[$i]['USERID'] . '">' . $res[$i]['USERID'] . '</option>';
        }
        // for agregation sites 
        $query = "SELECT  USERID FROM mbluser where userid like '%tpagg%' order by USERID";
        $res = $lobjcon->Get_Array($query);
        $agregation = '<option value="">-- Please Select --</option>';
        for ($i = 0; $i < count($res); $i++) {
            $agregation .= '<option value="' . $res[$i]['USERID'] . '">' . $res[$i]['USERID'] . '</option>';
        }
    } else if ($SLA_name == 'Jazz') {
        // for cell sites
        $query = "SELECT  USERID FROM mbluser where userid like '%jazzftts%' and USERID not in (select USERID from FTTT_SLA_MAPPING where fttt_sla=:SLA_name)order by USERID";
        // $res = $lobjcon->Get_Array($query);
        $params = [
            ':SLA_name' => $SLA_name
        ];

        $res = $dbObject->execSelect($query, $params);
        $towers = '<option value="">-- Please Select --</option>';
        for ($i = 0; $i < count($res); $i++) {
            $towers .= '<option value="' . $res[$i]['USERID'] . '">' . $res[$i]['USERID'] . '</option>';
        }
        $agregation = '';
    }
    // Added by Raja Usman Raza on 13-SEP-2021
    else if ($SLA_name == 'Zong') {
        // for cell sites
        $query = "SELECT  USERID FROM mbluser where userid like '%zongftts%' and USERID not in (select USERID from FTTT_SLA_MAPPING where fttt_sla=:SLA_name)order by USERID";
        // $res = $lobjcon->Get_Array($query);
        $params = [
            ':SLA_name' => $SLA_name
        ];

        $res = $dbObject->execSelect($query, $params);
        // var_dump($res);exit;

        $towers = '<option value="">-- Please Select --</option>';
        for ($i = 0; $i < count($res); $i++) {
            $towers .= '<option value="' . $res[$i]['USERID'] . '">' . $res[$i]['USERID'] . '</option>';
        }
        $agregation = '';
    }
    // Raja Usman Raza Code Ends here


    $result[0] = $towers;
    $result[1] = $agregation;

    if (isset($towers) && isset($agregation)) {

        echo  json_encode($result);
    } else {
        $result[0] = 'NO_DATA';
        echo json_encode($result);
    }
} else if ($data_type == "CREATE_MAPPING") {

    $SLAname = $_POST['SLAname'];
    $towerID = $_POST['towerID'];
    $tid_sla = $_POST['tid_sla'];
    $Ag_ID = $_POST['Ag_ID'];
    $Ag_ID_SLA = $_POST['Ag_ID_SLA'];
    $operator = $_SESSION['suuser1'];


    $query = "INSERT INTO FTTT_SLA_MAPPING (id,fttt_sla,userid,siteid,agregationid,ag_siteid,datetime,operator) values
    (FTTT_SLA_MAPPING_ID_SEQ.nextval,:fttt_sla,:towerID,:siteid,:agregationid,:ag_siteid,sysdate,:operator)";

    // $query = "INSERT INTO FTTT_SLA_MAPPING (id,fttt_sla,userid,siteid,agregationid,ag_siteid,datetime,operator) values
    // (FTTT_SLA_MAPPING_ID_SEQ.nextval,'$SLAname','$towerID','$tid_sla','$Ag_ID','$Ag_ID_SLA',sysdate,'$operator')";

    // if ($lobjcon->Add_Row($query)) {

    //     $dat = 'success';
    //     echo json_encode($dat);
    // } else {
    //     $dat = 'fail';
    //     echo json_encode($dat);
    // }

    $params = [
        ':fttt_sla' => $SLAname,
        ':towerID' => $towerID,
        ':siteid' => $tid_sla,
        ':agregationid' => $Ag_ID,
        ':ag_siteid' => $Ag_ID_SLA,
        ':operator' => $operator
    ];

    if ($dbObject->execInsertUpdate($query, $params)) {
        $dat = 'success';
        echo json_encode($dat);
    } else {
        $dat = 'fail';
        echo json_encode($dat);
    }
} else if ($data_type == "GET_MAPPING") {
    $SLA_name = $_POST['sla'];
    // $query = "select * from FTTT_SLA_MAPPING where fttt_sla='$SLA_name' order by ID";
    // $res = $lobjcon->Get_Array($query);
    $query = "SELECT * 
          FROM FTTT_SLA_MAPPING 
          WHERE fttt_sla = :SLA_name 
          ORDER BY ID";

    $params = [
        ':SLA_name' => $SLA_name
    ];

    $res = $dbObject->execSelect($query, $params);

    // var_dump($query);
    // exit;

    // fetching deartment of user 
    // $query = "select d.subdept from professionalinfo pi join deptdesignation d on d.deptdesigid=pi.dept_desig_id where pi.empid='" . $_SESSION['suuser1'] . "'";
    $query = "select d.subdept from professionalinfo pi join deptdesignation d on d.deptdesigid=pi.dept_desig_id where pi.empid=:empId";

    // $userdept = $lobjcon->Get_Array($query);
    $params = [
        ':empId' => $_SESSION['suuser1']
    ];

    // Execute the query using binded parameters
    $userdept = $dbObject->execSelect($query, $params);

    for ($i = 0; $i < count($res); $i++) {
        $result[$i][0] = $i + 1;
        $result[$i][1] = $res[$i]['FTTT_SLA'];
        $result[$i][2] = $res[$i]['USERID'];
        $result[$i][3] = '<input type="text" id="SLA_SITE' . $res[$i]['ID'] . '" class="form-control" value="' . $res[$i]['SITEID'] . '">';
        if ($SLA_name == 'Telenor') {
            // for agregation sites 
            $query = "SELECT  USERID FROM mbluser where userid like '%tpagg%' order by USERID";
            $inerresult = $lobjcon->Get_Array($query);
            // $safeID = htmlspecialchars($res[$i]['ID'], ENT_QUOTES, 'UTF-8');
            $agregation = '<select name="Agregation" id="AGR' . htmlspecialchars($res[$i]['ID'], ENT_QUOTES, 'UTF-8') . '" class="form-control search-select" required>';
            for ($j = 0; $j < count($inerresult); $j++) {
                $agregation .= '<option value="' . $inerresult[$j]['USERID'];

                if ($inerresult[$j]['USERID'] == $res[$i]['AGREGATIONID']) {
                    $agregation .= '" selected  >"' . $inerresult[$j]['USERID'] . '"</option>';
                } else {
                    $agregation .= '" >' . $inerresult[$j]['USERID'] . '</option>';
                }
            }
            $agregation .= '</select>';

            $result[$i][4] =  $agregation;
        } else if ($SLA_name == 'Jazz') {
            $result[$i][4] =  '<input type="text" id="AGR' . $res[$i]['ID'] . '" class="form-control" value="' . $res[$i]['AGREGATIONID'] . '">';
        }

        // Added by Raja Usman Raza 13-SEP-2021
        else if ($SLA_name == 'Zong') {
            $result[$i][4] =  '<input type="text" id="AGR' . $res[$i]['ID'] . '" class="form-control" value="' . $res[$i]['AGREGATIONID'] . '">';
        }
        // Usman Raza Code Ends Here 

        $result[$i][5] = '<input type="text" id="SLA_AGR' . $res[$i]['ID'] . '" class="form-control" value="' . $res[$i]['AG_SITEID'] . '">';
        if ($userdept[0]['SUBDEPT'] == 'Access' || $userdept[0]['SUBDEPT'] == 'Enterprise Solutions') {
            $result[$i][6] = '	<input type="submit" class="btn btn-primary" onclick="updateMapping(' . $res[$i]['ID'] . ');" id="button" value="Update" />';
        } else {
            $result[$i][6] = 'not allowed';
        }
    }
    echo json_encode($result);
} else if ($data_type == "UPDATE_MAPPING") {

    $ID = $_POST['ID'];
    $SLA_CELLSITE = $_POST['SLA_CELLSITE'];
    $AGR = $_POST['AGR'];
    $SLA_AGR = $_POST['SLA_AGR'];
    $operator = $_SESSION['suuser1'];

    // $query = "update FTTT_SLA_MAPPING set SITEID='$SLA_CELLSITE',agregationid='$AGR',ag_siteid='$SLA_AGR' where id='$ID'";
    $query = "update FTTT_SLA_MAPPING set SITEID=:SLA_CELLSITE ,agregationid=:AGR,ag_siteid=:SLA_AGR where id=:ID'";

    // if ($lobjcon->Add_Row($query)) {

    //     $logQuerry = "INSERT INTO FTTT_SLA_MAPPING_OPS  (id,mapping_id,operation_type,operation_subtype,operation_value,operation_subvalue,datetime,operator) VALUES
    // (FTTT_SLA_MAPPING_OPS_ID_SEQ.nextval,$ID,'UPDATED','$SLA_CELLSITE','$AGR','$SLA_AGR',sysdate,'$operator')";
    //     $lobjcon->Add_Row($logQuerry);

    //     $dat = 'success';
    //     echo json_encode($dat);
    // } else {
    //     $dat = 'fail';
    //     echo json_encode($dat);
    // }
    $updateParams = [
        ':SLA_CELLSITE' => $SLA_CELLSITE,
        ':AGR' => $AGR,
        ':SLA_AGR' => $SLA_AGR,
        ':ID' => $ID,
    ];

    // Execute the update query
    if ($dbObject->execInsertUpdate($query, $updateParams)) {
        $logQuerry = "INSERT INTO FTTT_SLA_MAPPING_OPS (id, mapping_id, operation_type, operation_subtype, operation_value, operation_subvalue, datetime, operator) 
        VALUES ( FTTT_SLA_MAPPING_OPS_ID_SEQ.nextval, :ID, 'UPDATED', :SLA_CELLSITE, :AGR, :SLA_AGR, sysdate, :operator
        )";

        $logParams = [
            ':ID' => $ID,
            ':SLA_CELLSITE' => $SLA_CELLSITE,
            ':AGR' => $AGR,
            ':SLA_AGR' => $SLA_AGR,
            ':operator' => $operator,
        ];

        // Execute the log query
        $dbObject->execInsertUpdate($logQuerry, $logParams);

        $dat = 'success';
        echo json_encode($dat);
    } else {
        $dat = 'fail';
        echo json_encode($dat);
    }
} else if ($data_type == "ESCLATION_MAPPING") {
    $userid = $_POST['user'];
    $ticketid = $_POST['ticket'];
    $outagetype = $_POST['outagetype'];
    $operator = $_SESSION['suuser1'];
    $startdatetime = $_POST['startdatetime'];
    $issue_reported_by = $_POST['issue_reported_by'];
    $user_type = $_POST['user_type'];
    $actionvalue = $_POST['actionvalue'];
    $date = trim($_POST['date']); //date('d-M-Y H:i:s', strtotime($_POST['date'])) ;
    //updated by anum.rafaqat 18-oct-2022
    $fault_type = trim($_POST['fault_type']);
    $startdatetime = trim($_POST['startdatetime']);




    /*anum.rafaqat - commented by
    @date: 25-JUL-2022
    ticket can be null now incase of non existent ids */

    // $query = "select * from SLA_FTTT_ESCLAIONS where TTID='$ticketid' and USERID='$userid' and status='active'";
    // $countcheck = $lobjcon->Get_Array($query);

    // if (!isset($countcheck) || is_null($countcheck) || empty($countcheck)) {

    /*anum.rafaqat - commented by
    @date: 25-JUL-2022
    ticket can be null now incase of non existent ids */
    //echo $actionvalue;exit;
    if ($user_type == 'legitimate') {

        if ($actionvalue == 'Historicalevent') {
            $data = explode('-', $userid);
            $user = $data[0];
            $ticket = $data[1];

            // $InsQuery = "insert into SLA_FTTT_ESCLAIONS (ID,USERID,TTID,OUTAGE_TYPE,DATETIME,OPERATOR,STATUS,ESCALTION_LEVEL,STARTTIME,ISSUE_REPORTED_BY, USER_TYPE) 
            // VALUES (SLA_FTTT_ESCLAIONS_ID_SEQ.nextval,'$user','$ticket','$outagetype',TO_DATE ('$date', 'DD-MON-YYYY HH24:MI:SS'),'$operator','active','0',TO_DATE ('$startdatetime', 'DD-MON-YYYY HH24:MI:SS'),'$issue_reported_by','$user_type')";

            $InsQuery = "insert into SLA_FTTT_ESCLAIONS (ID,USERID,TTID,OUTAGE_TYPE,DATETIME,OPERATOR,STATUS,ESCALTION_LEVEL,STARTTIME,ISSUE_REPORTED_BY, USER_TYPE) 
            VALUES (SLA_FTTT_ESCLAIONS_ID_SEQ.nextval,:user_param,:tickets,:outagetypes,TO_DATE(:dates, 'DD-MON-YYYY HH24:MI:SS'),:operator,'active','0',TO_DATE(:startdatetimes, 'DD-MON-YYYY HH24:MI:SS'),:issue_reported_bys,:user_types)";
            // var_dump($InsQuery);
            // if ($lobjcon->Add_Row($InsQuery))
            $params = [
                ':user_param' => $user,
                ':tickets' => $ticket,
                ':outagetypes' => $outagetype,
                ':dates' => $date,
                ':operator' => $operator,
                ':startdatetimes' => $startdatetime,
                ':issue_reported_bys' => $issue_reported_by,
                ':user_types' => $user_type
            ];

            if ($dbObject->execInsertUpdate($InsQuery, $params)) {

                $dat = 'success';
                echo json_encode($dat);
            } else {
                $dat = 'fail';
                echo json_encode($dat);
            }
        } else if ($actionvalue == 'Newevent') {
            for ($i = 0; $i < count($userid); $i++) {
                $forwardTo = "";
                $data = explode('-', $userid[$i]);
                $user = $data[0];
                $city = $data[1];
                if ($city == 'Islamabad') {
                    $forwardTo = 'TxOandM';
                } else if ($city == 'Rawalpindi') {
                    $forwardTo = 'TxOandMRWP';
                } else if ($city == 'Faisalabad') {
                    $forwardTo = 'tx-OandMfsd';
                } else if ($city == 'Peshawar') {
                    $forwardTo = 'tx-oandm2-psh';
                }
                // added byanum.rafaqat 31-DEC-2022
                else if ($city == 'Sargodha') {
                    $forwardTo = 'tx-OandM-sgd';
                } else if ($city == 'Multan') {
                    $forwardTo = 'tx-OandM-mtn';
                } else if ($city == 'Sialkot') {
                    $forwardTo = 'tx-OandM-skt';
                } else if ($city == 'Lahore') {
                    $forwardTo = 'TxOandMLHR';
                }

                // added byanum.rafaqat 31-DEC-2022

                // added byanum.rafaqat 06-JAN-2022
                else if ($city == 'Bahawalpur') {
                    $forwardTo = 'tx-OandM-mtn';
                } else if ($city == 'Gujrat') {
                    $forwardTo = 'TxOandMRWP';
                } else if ($city == 'Gujranwala') {
                    $forwardTo = 'tx-oandmgrw';
                } else if ($city == 'Sahiwal') {
                    $forwardTo = 'tx-OandMfsd';
                } else if ($city == 'Taxila') {
                    $forwardTo = 'tx-OandMtxa';
                } else if ($city == 'Sheikhupura') {
                    // $forwardTo = 'tx-OandMfsd';
                    $forwardTo = 'TxOandMLHR';
                } else if ($city == 'Kasur') {
                    // $forwardTo = 'tx-OandMfsd';
                    $forwardTo = 'TxOandMLHR';
                }
                // added byanum.rafaqat 06-JAN-2022

                // updated by anum.rafaqat 9-DEC-2022

                /**
                 * start 
                 * @Author : Amna Rashid
                 * Dated: 30-MAY-2024
                 * Comment: this check is added by amna on request of NOC to avoid making tickets 
                 * forwarded to none when no mapping against city is found.
                 **/
                // var_dump('$user: ', $user);
                // $user = trim($user);
                if ($forwardTo != "" && !empty($forwardTo)) {

                    $qu1 = "select a.USERID,a.CUSTOMERPRIORITY from MBLUSER a,cnbs.users_status b where a.USERID=b.USERID and a.USERID=:user_param  and b.reason != 'completed'";
                    // $userPriority = $lobjcon->Get_Array($qu1);
                    $params = [
                        ':user_param' => $user
                    ];

                    $userPriority = $dbObject->execSelect($qu1, $params);
                    $priority = $userPriority[0]['CUSTOMERPRIORITY'];

                    $maxidtt = $newttObj->getLastTTId();
                    //updated description by anum.rafaqat 17-Jan-2023
                    $createComment = $operator . ' has generated the ticket, please check physical link.';
                    $createCommentForward = '(' . date('d-m-Y h:i:s') . ')/ticket forwarded to ' . $forwardTo;
                    //adding TT (main table)...
                    // $insertTT = $newttObj->addTroubleTicket ( $maxidtt, $userid, 'CUSTOMER', $createComment, 'Complaint', 'Physical Link', 'Low Optical Power', '', 'n',$forwardTo,$forwardTo, 'ONT-ALARMS-INTIMATION', 'ONT-ALARMS-INTIMATION', '');

                    $insertTT = $newttObj->addTroubleTicketNew($maxidtt, $user, 'CUSTOMER', $createComment, 'Complaint', $fault_type, 'Down', '', 'n', $forwardTo, $forwardTo, 'SLA_FTTT_ESCALATIONS', 'SLA_FTTT_ESCALATIONS', '', '', $city, $sector);

                    if ($insertTT) {
                        //adding tt issue detail...s
                        $ttDetails = $newttObj->addTTDetails($maxidtt, 'ISSUE TT',  $fault_type, 'Down', '', '', 'SLA_FTTT_ESCALATIONS', $createComment, '', '', '', 'open', $forwardTo, '');
                    }
                    if ($ttDetails) {
                        //adding tt issue detail...SLA CHECK active
                        $ttDetails1 = $newttObj->addTTDetails($maxidtt, 'SLA CHECK',  'active', '0', '', '', 'SLA_FTTT_ESCALATIONS', $createComment, '', '', '', 'open', $forwardTo, '');
                    }
                    if ($ttDetails1) {
                        //adding ticket forwarding details...
                        $addFrwrdComment = $newttObj->addTTDetails(
                            $maxidtt,
                            'FORWARD TT',
                            $forwardTo,
                            '',
                            '',
                            '',
                            'SLA_FTTT_ESCALATIONS',
                            $createCommentForward,
                            '',
                            '',
                            '',
                            'open',
                            $forwardTo,
                            ''
                        );
                    }


                    $InsQuery = "insert into SLA_FTTT_ESCLAIONS (ID,USERID,TTID,OUTAGE_TYPE,DATETIME,OPERATOR,STATUS,ESCALTION_LEVEL,STARTTIME,ISSUE_REPORTED_BY, USER_TYPE) 
                    VALUES (SLA_FTTT_ESCLAIONS_ID_SEQ.nextval,:user_param,:maxidtt,:outagetype,TO_DATE(:dates, 'DD-MON-YYYY HH24:MI:SS'),:operator,'active','0',TO_DATE(:startdatetime, 'DD-MON-YYYY HH24:MI:SS'),:issue_reported_by,:user_type)";

                    $params = [
                        ':user_param' => $user,
                        ':maxidtt' => $maxidtt,
                        ':outagetype' => $outagetype,
                        ':dates' => $date,
                        ':operator' => $operator,
                        ':startdatetime' => $startdatetime,
                        ':issue_reported_by' => $issue_reported_by,
                        ':user_type' => $user_type
                    ];
                    // if ($lobjcon->Add_Row($InsQuery))
                    if ($dbObject->execInsertUpdate($InsQuery, $params)) {
                        // var_dump('priority', $priority);

                        // added by anum.rafaqat 09-DEC-2022
                        if ($priority == 'SLA (***)' || $priority == '***') {
                            // var_dump('priority');

                            $checkMessage = "";
                            $checkMessage .= "Complaint No: $maxidtt against userid:$user having priority: $priority has been generated. Please take necessary actions accordingly.<br><br>Regards,<br><br>NAYAtel Support";
                            $fromadd = 'do-not-reply@nayatel.com';
                            $mailto = 'noc@nayatel.com';
                            $subject = 'SLA/3 Star Complaint Alert ' . ' - ' . $user;
                            $hardCodeName = 'Nayatel Support';
                            $ccTo = '';

                            $ret = $mailSender->sendMailGeneral($fromadd, $mailto, $ccTo, $subject, $checkMessage, $hardCodeName);

                            if ($ret == 'send') {
                                $dat = 'successNeweventLegitimate';
                            }
                        }
                    } else {
                        $dat = 'fail';
                    }
                } else {
                    //donot forward TT
                }
                /**
                 * end 
                 * @Author : Amna Rashid
                 * Dated: 30-MAY-2024
                 * Comment: this check is added by amna on request of NOC to avoid making tickets 
                 * forwarded to none when no mapping against city is found.
                 **/
            }

            echo json_encode($dat);
        }
    } else if ($user_type == 'nonlegitimate') {
        $InsQuery = "insert into SLA_FTTT_ESCLAIONS (ID,USERID,TTID,OUTAGE_TYPE,DATETIME,OPERATOR,STATUS,ESCALTION_LEVEL,STARTTIME,ISSUE_REPORTED_BY, USER_TYPE) 
         VALUES (SLA_FTTT_ESCLAIONS_ID_SEQ.nextval,:userid,:ticketid,:outagetype,TO_DATE (:dates, 'DD-MON-YYYY HH24:MI:SS'),:operator,'active','0',TO_DATE (:startdatetime, 'DD-MON-YYYY HH24:MI:SS'),:issue_reported_by,:user_type)";
        $params = [
            ':userid' => $userid,
            ':ticketid' => $ticketid,
            ':outagetype' => $outagetype,
            ':dates' => $date,
            ':operator' => $operator,
            ':startdatetime' => $startdatetime,
            ':issue_reported_by' => $issue_reported_by,
            ':user_type' => $user_type
        ];
        // var_dump($InsQuery);
        if ($dbObject->execInsertUpdate($InsQuery, $params)) {

            $dat = 'success';
            echo json_encode($dat);
        } else {
            $dat = 'fail';
            echo json_encode($dat);
        }
    }

    //anum.rafaqat
    //@date: 25-JUL-2022
} else if ($data_type == "GET_MAP_ESCLATIONS") {
    $operator = $_SESSION['suuser1'];
    $allowedarray = array("anum.rafaqat", "aroosa.nayyar", "salman.zia", "haris.salim", "afad.amir", "saqib.kamran", "mahnoor.mustajab", "mursleen.amjad", "ali.malik", "khurram.saleem", "saleha.saleem"); ////////// allowed personal to update time even after additin once

    $daterange = explode('-', $_POST['date']);
    // var_dump('daterange', $daterange);
    $date1 = str_replace('/', '-', $daterange[0]);
    $date2 = str_replace('/', '-', $daterange[1]);
    $date1 = explode('-', $date1);
    $date2 = explode('-', $date2);

    $startdate = $date1[1] . '-' . $date1[0] . '-' . $date1[2];
    $startdate = date('d-M-Y', strtotime($startdate)) . '00:00:00';
    $enddate = $date2[1] . '-' . trim($date2[0]) . '-' . $date2[2];
    $enddate = date('d-M-Y', strtotime($enddate)) . '23:59:59';
    // var_dump($startdate);  // Check the format of startdate
    // var_dump($enddate);


    // <!-- Mursleen Amjad DELAY_TIME,NOC_COMMENT added-->
    $query = "select b.CLOSE_TIME,a.ID,a.USERID,a.TTID,a.OUTAGE_TYPE,a.EVENT_ID,a.USER_TYPE,a.OPERATOR,TO_CHAR(a.DATETIME,'DD-MON-YYYY HH24:MI:SS')DATETIME,a.ISSUE_REPORTED_BY
    ,a.EMAIL_SUBJECT,a.OUTAGE_REASON,TO_CHAR(a.STARTTIME,'DD-MON-YYYY HH24:MI:SS')STARTTIME,TO_CHAR(a.SLA_INT_TIME,'DD-MON-YYYY HH24:MI:SS')SLA_INT_TIME,TO_CHAR(a.END_TIME,'DD-MON-YYYY HH24:MI:SS')END_TIME ,
    ROUND((END_TIME-SLA_INT_TIME)*24*60,1) TIME_SLA_INT,
    ROUND((END_TIME-STARTTIME)*24*60,1) TOTAL_TIME,
    a.DELAY_TIME,
    a. NOC_COMMENT
    from SLA_FTTT_ESCLAIONS a join ntlcrm.troubleticket b on a.TTID=b.ID  where DATETIME>=TO_DATE(:startdate,'DD-MON-YYYY HH24:MI:SS')and DATETIME<=TO_DATE(:enddate,'DD-MON-YYYY HH24:MI:SS')";
    // $res = $lobjcon->Get_Array($query);
    $params = [
        ':startdate' => $startdate, // Start date parameter
        ':enddate' => $enddate     // End date parameter
    ];
    $res = $dbObject->execSelect($query, $params);

    //var_dump($res);exit;

    //anum.rafaqa 
    //@date: 26-JUL-2022

    $event_ids = "select * from EVENTLOGGERFORM where status='ACTIVE'";
    $result_eventno =  $lobjcon->Get_Array($event_ids);

    //var_dump($result_eventno);exit;
    //$agregation1='<select class="form-control" name="event_id" id="event_id' . $res[$i]['ID'] .'">';

    //var_dump($agregation4);exit;


    //$result[$i][2] = $agregation1.=$agregation2;
    //var_dump($result[$i][2]);
    //anum.rafaqa 
    //@date: 26-JUL-2022
    for ($i = 0; $i < count($res); $i++) {

        //added by anum.rafaqat
        //@date: 25-jul-2022
        $result[$i][0] = $i + 1;
        $result[$i][1] = '<input type="text"  class="form-control datetimepicker" style="width:150px;"   name="datetime" id="datetime' . $res[$i]['ID'] . '" value="' . $res[$i]['DATETIME'] . '">'; //ADDED BY ANUM
        //add_selection(1,3);exit;
        $event_select = make_select('event_id', $res[$i]['EVENT_ID'], $res[$i]['ID']);
        $result[$i][2] = $event_select;
        //ADDED BY ANUM 
        $hostname = get_cfg_var('server_dns');
        if ($res[$i]['USER_TYPE'] == 'legitimate') {
            $result[$i][3] = '<a target="_blank" href="https://' . $hostname . '/views/crmViews/nayatelCrm/complaint_detatil.php?lid=' . $res[$i]['USERID'] . '&tid=' . $res[$i]['TTID'] . '">' . $res[$i]['TTID'] . '</a>';
        } else if ($res[$i]['USER_TYPE'] == 'nonlegitimate') {
            //updated .. anum.rafaqat
            $result[$i][3] = '<input type="text" onkeypress="return isNumberKey(event)" class="form-control" name="TTID" id="TTID' . $res[$i]['ID'] . '" value="' . $res[$i]['TTID'] . '">';
        } else {
            $result[$i][3] = $res[$i]['TTID'];
        }
        //$result[$i][3] = $res[$i]['TTID'];

        // $agregation3='<select class="form-control" name="USERID" id="USERID' . $res[$i]['ID'] .'">';
        // $result[$i][4] = $agregation3.=$agregation4;
        if ($res[$i]['USER_TYPE'] == 'legitimate') {
            $result[$i][4] = $res[$i]['USERID'];
        } else if ($res[$i]['USER_TYPE'] == 'nonlegitimate') {
            $result[$i][4] = '<input type="text" class="form-control" name="USERID" id="USERID' . $res[$i]['ID'] . '" value="' . $res[$i]['USERID'] . '">';
        } else {
            $result[$i][4] = $res[$i]['USERID'];
        }

        $safeID = htmlspecialchars($res[$i]['ID'], ENT_QUOTES, 'UTF-8');
        $result[$i][5] = '<select class="form-control" name="outage_type" id="outage_type' . htmlspecialchars($res[$i]['ID'], ENT_QUOTES, 'UTF-8') . '">'; //ADDED BY ANUM

        if ($res[$i]["OUTAGE_TYPE"] == "Single Cut") {
            $appendStr = '<option value="">--Please Select--</option>
        <option value = "Single Cut" selected>Single Cut</option>
        <option value = "Dual Cut" >Dual Cut</option>';
        } else if ($res[$i]["OUTAGE_TYPE"] == "Dual Cut") {
            $appendStr  = '<option value="">--Please Select--</option>
        <option value = "Single Cut">Single Cut</option>
        <option value = "Dual Cut" selected>Dual Cut</option>';
        } else {
            $appendStr  = '<option value="">--Please Select--</option>
        <option value = "Single Cut">Single Cut</option>
        <option value = "Dual Cut" >Dual Cut</option>';
        }
        $result[$i][5] .= $appendStr . '</select>';


        // $result[$i][6] = '<select class="form-control" name="issue_reported_by" id="issue_reported_by' . $res[$i]['ID'] . '">';
        $result[$i][6] = '<select class="form-control" name="issue_reported_by" id="issue_reported_by' . htmlspecialchars($res[$i]['ID'], ENT_QUOTES, 'UTF-8') . '">';

        //updated .. anum.rafaqat
        if ($res[$i]["ISSUE_REPORTED_BY"] == "NTL") {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL" selected>NTL</option>
        <option  value="TP">TP</option>
        <option  value="JAZZ">JAZZ</option>
        <option  value="ZONG">ZONG</option>
        <option  value="TELENOR">TELENOR</option>';
        } else if ($res[$i]["ISSUE_REPORTED_BY"] == "TP") {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL">NTL</option>
        <option  value="TP" selected>TP</option>
        <option  value="JAZZ">JAZZ</option>
        <option  value="ZONG">ZONG</option>
        <option  value="TELENOR">TELENOR</option>';
        } else if ($res[$i]["ISSUE_REPORTED_BY"] == "JAZZ") {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL">NTL</option>
        <option  value="TP">TP</option>
        <option  value="JAZZ" selected>JAZZ</option>
        <option  value="ZONG">ZONG</option>
        <option  value="TELENOR">TELENOR</option>';
        } else if ($res[$i]["ISSUE_REPORTED_BY"] == "ZONG") {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL">NTL</option>
        <option  value="TP">TP</option>
        <option  value="JAZZ" >JAZZ</option>
        <option  value="ZONG" selected>ZONG</option>
        <option  value="TELENOR">TELENOR</option>';
        } else if ($res[$i]["ISSUE_REPORTED_BY"] == "TELENOR") {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL">NTL</option>
        <option  value="TP">TP</option>
        <option  value="JAZZ" >JAZZ</option>
        <option  value="ZONG" >ZONG</option>
        <option  value="TELENOR" selected>TELENOR</option>';
        } else {
            $appendStr1 = '<option value="">--Please Select--</option>
        <option value="NTL">NTL</option>
        <option  value="TP">TP</option>
        <option  value="JAZZ">JAZZ</option>
        <option  value="ZONG">ZONG</option>
        <option  value="TELENOR">TELENOR</option>';
        }

        //updated .. anum.rafaqat
        $result[$i][6] .= $appendStr1 . '</select>';
        //ADDED BY ANUM
        $result[$i][7] = '<input type="text" class="form-control" name="email_subject" id="email_subject' . $res[$i]['ID'] . '" value="' . $res[$i]['EMAIL_SUBJECT'] . '">';
        $result[$i][8] = '<input type="text" class="form-control datetimepicker" style="width:150px;" name="Starttime" id="starttime' . $res[$i]['ID'] . '"value="' . $res[$i]['STARTTIME'] . '">';
        $result[$i][9] = '<input type="text" class="form-control datetimepicker" style="width:150px;" name="SLA Intimation" id="SLA_INT_TIME' . $res[$i]['ID'] . '"value="' . $res[$i]['SLA_INT_TIME'] . '">';
        $result[$i][10] = '<input type="text" class="form-control datetimepicker" style="width:150px;" name="End Time" id="endtime' . $res[$i]['ID'] . '"value="' . $res[$i]['END_TIME'] . '">';
        $result[$i][11] = $res[$i]['TOTAL_TIME'];
        $result[$i][12] = '<p >' . $res[$i]['TIME_SLA_INT'] . '</p>';

        // <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        // <!-- Mursleen Amjad -->
        $result[$i][13] = '<input type="number" class="form-control min="0" id="delayTime' . $res[$i]['ID'] . '"value="' . $res[$i]['DELAY_TIME'] . '" min="1">';
        $result[$i][14] = $res[$i]['TIME_SLA_INT'] - $res[$i]['DELAY_TIME'];
        $result[$i][15] = '<textarea id="comment' . $res[$i]['ID'] . '">' . $res[$i]['NOC_COMMENT'] . '</textarea>';
        $result[$i][16] = round($result[$i][14] / 60);  //actual downtime(telco - ) devided by 60 Delay
        // <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        $result[$i][17] =  '<input type="text" class="form-control" name="outage_reason" id="outage_reason' . $res[$i]['ID'] . '" value="' . $res[$i]['OUTAGE_REASON'] . '">';
        //updated .. anum.rafaqat
        $result[$i][18] =    $res[$i]['OPERATOR'] . '(' . $res[$i]['DATETIME'] . ')';

        //updated .. anum.rafaqat

        //updated editable always
        //@date: 18-aug-2022


        //   <input type="submit" class="btn btn-primary" id="update' . $res[$i]['ID'] . '" onclick="Update_TIMES(\''.$res[$i]['ID'].'\',\''.$res[$i]['USER_TYPE'].'\');" value="UPDATE" />
        // <input type="submit" class="btn btn-danger" id="DELETE' . $res[$i]['ID'] . '" onclick="DELETE(\''.$res[$i]['ID'].'\',\''.$res[$i]['USER_TYPE'].'\',\''.$res[$i]['TTID'].'\');" value="CLOSE" />
        //     $TT=$res[$i]['TTID'];
        //     $query_closetime = "select * from ntlcrm.troubleticket where ID='$TT'";
        // $result = $lobjcon->Get_Array($query_closetime);
        //print_r($result);exit;

        // $result[$i][19] = '';
        // print_r($result[0]['CLOSE_TIME']);
        //    $query1 = "SELECT * FROM NTLCRM.TROUBLETICKETDETAIL WHERE TICKETID ='$res[$i]['TTID']' and operationtype = 'FIN PROBLEM' AND operationvalue is not null;";
        //    $data_fin_problem = $lobjcon->Get_Array($query);
        //    echo count($data_fin_problem);exit;

        // //  OR (operationtype = 'FIN SOLUTION' AND operationvalue is not null)) ORDER BY ID DESC;";   /
        //     $query2 = "SELECT * FROM NTLCRM.TROUBLETICKETDETAIL WHERE TICKETID ='$res[$i]['TTID']' and operationtype = 'FIN SOLUTION' AND operationvalue is not null;";
        //     $data_fin_solution = $lobjcon->Get_Array($query);



        if (in_array($operator, $allowedarray)) {

            if ($res[$i]['CLOSE_TIME'] != '') {
                $result[$i][19] = '  <div class="row" style="padding: 8px">
            <button  class="btn btn-primary btn-sm" id="update' . $res[$i]['ID'] . '" type="submit" onclick="Update_TIMES(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\');">
            <i class="fa fa-edit"> </i>
        </button>
        <button  class="btn btn-danger btn-sm" id="DELETE' . $res[$i]['ID'] . '" onclick="DELETE(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\',\'' . $res[$i]['TTID'] . '\');" disabled >
        <i class="	glyphicon glyphicon-remove"> </i>
    </button>
    </div>  ';
            } else {
                $result[$i][19] = '  <div class="row" style="padding: 8px">
                <button  class="btn btn-primary btn-sm" id="update' . $res[$i]['ID'] . '" type="submit" onclick="Update_TIMES(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\');">
                <i class="fa fa-edit"> </i>
            </button>
            <button  class="btn btn-danger btn-sm" id="DELETE' . $res[$i]['ID'] . '" onclick="DELETE(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\',\'' . $res[$i]['TTID'] . '\');" >
            <i class="	glyphicon glyphicon-remove"> </i>
        </button>
        </div>  ';
            }
        } else {

            if ($res[$i]['CLOSE_TIME'] != '') {

                $result[$i][19] = '<div class="row" style="padding: 8px">
        <button  class="btn btn-primary btn-sm" id="add' . $res[$i]['ID'] . '" onclick="INSERT_TIMES(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\');">
            <i class="fa fa-plus"> </i>
        </button>
        <button  class="btn btn-danger btn-sm" id="DELETE' . $res[$i]['ID'] . '" onclick="DELETE(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\',\'' . $res[$i]['TTID'] . '\');" disabled>
        <i class="	glyphicon glyphicon-remove"> </i>
    </button> </div>';
            } else {
                $result[$i][19] = '<div class="row" style="padding: 8px">
            <button  class="btn btn-primary btn-sm" id="add' . $res[$i]['ID'] . '" onclick="INSERT_TIMES(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\');">
                <i class="fa fa-plus"> </i>
            </button>
            <button  class="btn btn-danger btn-sm" id="DELETE' . $res[$i]['ID'] . '" onclick="DELETE(\'' . $res[$i]['ID'] . '\',\'' . $res[$i]['USER_TYPE'] . '\',\'' . $res[$i]['TTID'] . '\');">
            <i class="	glyphicon glyphicon-remove"> </i>
        </button> </div>';
            }
        }

        //updated editable always
        //@date: 18-aug-2022


    }
    //echo "ANJM";
    // print_r($result);exit;
    echo json_encode($result);
    exit;
}  //added by anum.rafaqat
//@date: 25-jul-2022
else if ($data_type == "SET_ESCLATION_TIME") {
    $ID = $_POST['id'];
    $startdate = trim($_POST['starttime']);
    $sla_int_time = trim($_POST['sal_int_time']);
    $enddate = trim($_POST['endtime']);
    $operator = $_SESSION['suuser1'];
    // var_dump($_POST);exit;
    //added by anum.rafaqat
    //@date: 25-jul-2022
    $outage_type = trim($_POST['outage_type']);
    $issue_reported_by = trim($_POST['issue_reported_by']);
    $email_subject = trim($_POST['email_subject']);
    $delaytime = trim($_POST['delaytime']);
    $comment = trim($_POST['comment']);
    $event_id = trim($_POST['event_id']);
    $outage_reason = trim($_POST['outage_reason']);
    $userid = trim($_POST['userid']);
    $ttid = trim($_POST['ttid']);
    $user_type = trim($_POST['user_type']);
    $datetime = trim($_POST['datetime']);

    //   var_dump($datetime);
    // var_dump($ttid);
    // var_dump($userid);
    // var_dump($delaytime);
    //  var_dump($outage_type);
    //  var_dump($startdate);
    //  var_dump($sla_int_time);
    //  var_dump($enddate);
    // //exit;
    // if($user_type == ''){
    //     echo 'khaali';
    // }else{
    //     echo 'not khaali';
    // }

    //added by anum.rafaqat
    //@date: 25-jul-2022
    if ($startdate != '' && !is_null($startdate) && $sla_int_time != '' && !is_null($sla_int_time) && $enddate != '' && !is_null($enddate)) {
        if ($user_type == 'legitimate' || $user_type == '') {
            //echo "daddsa";
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS')
        ,sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),end_time=TO_DATE (:enddate, 'DD-MON-YYYY HH24:MI:SS'),ISSUE_REPORTED_BY=:issue_reported_by,OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
         DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:id";
            $params = [
                ':startdate' => $startdate,
                ':sla_int_time' => $sla_int_time,
                ':enddate' => $enddate,
                ':issue_reported_by' => $issue_reported_by,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':datetime' => $datetime,
                ':id' => $ID,
            ];
        } else if ($user_type == 'nonlegitimate') {
            //echo "bla bla";
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS')
             ,sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),end_time=TO_DATE (:enddate, 'DD-MON-YYYY HH24:MI:SS'),ISSUE_REPORTED_BY=:issue_reported_by,OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
            DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,USERID=:userid,TTID=:ttid,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:id";
            $params = [
                ':startdate' => $startdate,
                ':sla_int_time' => $sla_int_time,
                ':enddate' => $enddate,
                ':issue_reported_by' => $issue_reported_by,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':userid' => $userid,
                ':ttid' => $ttid,
                ':datetime' => $datetime,
                ':id' => $ID,
            ];
        }
        //echo 'hello';
    } else if ($startdate != '' && $sla_int_time != '') {
        if ($user_type == 'legitimate' || $user_type == '') {
            // echo "dsfds";
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS')
          ,sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),ISSUE_REPORTED_BY=:issue_reported_by,OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
          DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID";
            $params = [
                ':startdate' => $startdate,
                ':sla_int_time' => $sla_int_time,
                ':issue_reported_by' => $issue_reported_by,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        } else {
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS')
        ,sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),ISSUE_REPORTED_BY=:issue_reported_by,OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
        DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,USERID=:userid,TTID=:ttid,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID";
            $params = [
                ':startdate' => $startdate,
                ':sla_int_time' => $sla_int_time,
                ':issue_reported_by' => $issue_reported_by,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':userid' => $userid,
                ':ttid' => $ttid,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        }
        // echo '1';
    } else if ($startdate != '' && $enddate != '') {
        //echo "anumu"; 
        if ($user_type == 'legitimate' || $user_type == '') {
            //updated....02-sept-2022 issue :/
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS')
          ,ISSUE_REPORTED_BY=:issue_reported_by,end_time=TO_DATE (:enddate, 'DD-MON-YYYY HH24:MI:SS'),sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
         DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID";
            $params = [
                ':startdate' => $startdate,
                ':issue_reported_by' => $issue_reported_by,
                ':enddate' => $enddate,
                ':sla_int_time' => $sla_int_time,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        } else {
            // echo "in last";exit;
            //updated by anum.rafaqat @date:19-aug-2022
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS'),sla_int_time=TO_DATE (:sla_int_time, 'DD-MON-YYYY HH24:MI:SS'),ISSUE_REPORTED_BY=:issue_reported_by,end_time=TO_DATE (:enddate, 'DD-MON-YYYY HH24:MI:SS'),OUTAGE_TYPE=:outage_type,EMAIL_SUBJECT=:email_subject,
            DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason,EVENT_ID=:event_id,OPERATOR=:operator,USERID=:userid,TTID=:ttid,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID";
            $params = [
                ':startdate' => $startdate,
                ':sla_int_time' => $sla_int_time,
                ':issue_reported_by' => $issue_reported_by,
                ':enddate' => $enddate,
                ':outage_type' => $outage_type,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':userid' => $userid,
                ':ttid' => $ttid,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        }
        // echo '2';
    } else {
        if ($user_type == 'legitimate' || $user_type == '') {
            //echo "misnah";
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS'),OUTAGE_TYPE=:outage_type,ISSUE_REPORTED_BY=:issue_reported_by,EMAIL_SUBJECT=:email_subject,
           DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason, EVENT_ID=:event_id,OPERATOR=:operator,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID";
            $params = [
                ':startdate' => $startdate,
                ':outage_type' => $outage_type,
                ':issue_reported_by' => $issue_reported_by,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        } else {
            //updated by anum.rafaqat @date:19-aug-2022
            $query = "update SLA_FTTT_ESCLAIONS SET STARTTIME=TO_DATE (:startdate, 'DD-MON-YYYY HH24:MI:SS'),OUTAGE_TYPE=:outage_type,ISSUE_REPORTED_BY=:issue_reported_by,EMAIL_SUBJECT=:email_subject,
          DELAY_TIME=:delaytime,NOC_COMMENT=:comment,OUTAGE_REASON=:outage_reason, EVENT_ID=:event_id,OPERATOR=:operator,USERID=:userid,TTID=:ttid,DATETIME=TO_DATE (:datetime, 'DD-MON-YYYY HH24:MI:SS') where ID=:ID'";
            $params = [
                ':startdate' => $startdate,
                ':outage_type' => $outage_type,
                ':issue_reported_by' => $issue_reported_by,
                ':email_subject' => $email_subject,
                ':delaytime' => $delaytime,
                ':comment' => $comment,
                ':outage_reason' => $outage_reason,
                ':event_id' => $event_id,
                ':operator' => $operator,
                ':userid' => $userid,
                ':ttid' => $ttid,
                ':datetime' => $datetime,
                ':ID' => $ID
            ];
        }
        //echo '3';
    }
    //echo "SADASDAS";
    if ($dbObject->execInsertUpdate($query, $params)) {
        // echo "hi hi";
        $query = "insert into SLA_FTTT_ESCALATION_OPS (ID,ESC_ID,OPERATIONTYPE,OPERATIONVALUE,OPERATIONSUBVALUE,COMMENTS,OPERATOR,DATETIME)
       VALUES(SLA_FTTT_ESCALATION_OPS_id_seq.nextval,:ID,'INSERTED',:startdate,:sla_int_time,:enddate,:operator,sysdate)";
        // $lobjcon->Add_Row($query);
        $params = [
            ':ID' => $ID,
            ':startdate' => $startdate,
            ':sla_int_time' => $sla_int_time,
            ':enddate' => $enddate,
            ':operator' => $operator
        ];
        $dbObject->execInsertUpdate($query, $params);

        $dat = 'success';
        echo json_encode($dat);
    } else {
        $dat = 'fail';
        echo json_encode($dat);
    }
} else if ($data_type == "UPDATE_ESCLATION_TIME") {
    $ID = $_POST['id'];

    //updated anum.rafaqat 30-aug-2022
    $startdate = trim($_POST['starttime']);
    $sla_int_time = trim($_POST['sal_int_time']);
    $enddate = trim($_POST['endtime']);
    //updated anum.rafaqat 30-aug-2022


    $operator = $_SESSION['suuser1'];
    // <!-- Mursleen Amjad -->
    $delaytime =  $_POST['delaytime'];
    $comment =  $_POST['comment'];
    // <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->

    //added by anum.rafaqat
    //@date: 25-jul-2022
    $outage_reason = trim($_POST['outage_reason']);
    $eventid = trim($_POST['event_id']);
    $outage_type = trim($_POST['outage_type']);
    $issue_reported_by = trim($_POST['issue_reported_by']);
    $email_subject = trim($_POST['email_subject']);

    $userid = trim($_POST['userid']);
    $ttid = trim($_POST['ttid']);
    $user_type = trim($_POST['user_type']);
    $datetime = trim($_POST['datetime']);



    //added by anum.rafaqat
    //@date: 25-jul-2022

    $start = '';
    $slatime = '';
    $end = '';
    if (isset($startdate) && $startdate != '') {
        if ((isset($enddate) && $enddate != '') || (isset($enddate) && $enddate != '')) {
            $start = "SET STARTTIME=TO_DATE ('$startdate', 'DD-MON-YYYY HH24:MI:SS'),";
        } else {
            $start = "SET STARTTIME=TO_DATE ('$startdate', 'DD-MON-YYYY HH24:MI:SS')";
        }
    }
    if (isset($enddate) && $enddate != '') {
        if (isset($enddate) && $enddate != '') {
            $end = "end_time=TO_DATE ('$enddate', 'DD-MON-YYYY HH24:MI:SS')";
        } else {
        }
    }
    if (isset($sla_int_time) && $sla_int_time != ' ') {
        if (isset($enddate) && $enddate != '') {
            $slatime = "sla_int_time=TO_DATE ('$sla_int_time', 'DD-MON-YYYY HH24:MI:SS'),";
        } else {
            $slatime = "sla_int_time=TO_DATE ('$sla_int_time', 'DD-MON-YYYY HH24:MI:SS')";
        }
    }
    $delay = ",delay_time =  '$delaytime'";
    $noc_comment =  ",noc_comment ='$comment'";
    //anum.rafaqat

    $outage_type = ",outage_type ='$outage_type'";
    $issue_reported_by = ",issue_reported_by ='$issue_reported_by'";
    $email_subject = ",email_subject ='$email_subject'";
    $event_id = ",event_id ='$eventid'";
    $outage_reason = ",outage_reason ='$outage_reason'";
    $operator = ",operator ='$operator'";

    $userid = ",userid ='$userid'";
    $datetime = ",datetime =TO_DATE ('$datetime', 'DD-MON-YYYY HH24:MI:SS')";
    $ttid = ",ttid ='$ttid'";


    if ($user_type == 'legitimate' || $user_type == '') {
        // $query = "update SLA_FTTT_ESCLAIONS $start $slatime $end $delay  $noc_comment  $outage_type $issue_reported_by $email_subject $event_id  $outage_reason  $operator  $datetime   where ID='$ID'";
        $query = "UPDATE SLA_FTTT_ESCLAIONS
            SET
                STARTTIME = TO_DATE(:start, 'DD-MON-YYYY HH24:MI:SS'),
                SLA_INT_TIME = TO_DATE(:slatime, 'DD-MON-YYYY HH24:MI:SS'),
                END_TIME = TO_DATE(:end, 'DD-MON-YYYY HH24:MI:SS'),
                DELAY_TIME = :delay,
                NOC_COMMENT = :noc_comment,
                OUTAGE_TYPE = :outage_type,
                ISSUE_REPORTED_BY = :issue_reported_by,
                EMAIL_SUBJECT = :email_subject,
                EVENT_ID = :event_id,
                OUTAGE_REASON = :outage_reason,
                OPERATOR = :operator,
                DATETIME = TO_DATE(:datetime, 'DD-MON-YYYY HH24:MI:SS')
            WHERE ID = :ID
        ";

        // Parameters for binding
        $params = [
            ':start' => $start,
            ':slatime' => $slatime,
            ':end' => $end,
            ':delay' => $delay,
            ':noc_comment' => $noc_comment,
            ':outage_type' => $outage_type,
            ':issue_reported_by' => $issue_reported_by,
            ':email_subject' => $email_subject,
            ':event_id' => $event_id,
            ':outage_reason' => $outage_reason,
            ':operator' => $operator,
            ':datetime' => $datetime,
            ':ID' => $ID
        ];
        //echo $query;exit;
    } else if ($user_type == 'nonlegitimate') {
        // $query = "update SLA_FTTT_ESCLAIONS $start $slatime $end $delay  $noc_comment  $outage_type $issue_reported_by $email_subject $event_id  $outage_reason  $operator  $datetime   $userid  $ttid where ID='$ID'";
        $query = "
            UPDATE SLA_FTTT_ESCLAIONS
            SET
                STARTTIME = TO_DATE(:start, 'DD-MON-YYYY HH24:MI:SS'),
                SLA_INT_TIME = TO_DATE(:slatime, 'DD-MON-YYYY HH24:MI:SS'),
                END_TIME = TO_DATE(:end, 'DD-MON-YYYY HH24:MI:SS'),
                DELAY_TIME = :delay,
                NOC_COMMENT = :noc_comment,
                OUTAGE_TYPE = :outage_type,
                ISSUE_REPORTED_BY = :issue_reported_by,
                EMAIL_SUBJECT = :email_subject,
                EVENT_ID = :event_id,
                OUTAGE_REASON = :outage_reason,
                OPERATOR = :operator,
                DATETIME = TO_DATE(:datetime, 'DD-MON-YYYY HH24:MI:SS'),
                USERID = :userid,
                TTID = :ttid
            WHERE ID = :ID
        ";

        // Parameters for binding
        $params = [
            ':start' => $start,
            ':slatime' => $slatime,
            ':end' => $end,
            ':delay' => $delay,
            ':noc_comment' => $noc_comment,
            ':outage_type' => $outage_type,
            ':issue_reported_by' => $issue_reported_by,
            ':email_subject' => $email_subject,
            ':event_id' => $event_id,
            ':outage_reason' => $outage_reason,
            ':operator' => $operator,
            ':datetime' => $datetime,
            ':userid' => $userid,
            ':ttid' => $ttid,
            ':ID' => $ID
        ];
    }
    //anum.rafaqat

    // echo  $query;
    // if ($lobjcon->Add_Row($query))
    if ($dbObject->execInsertUpdate($query, $params)) {

        $query = "insert into SLA_FTTT_ESCALATION_OPS (ID,ESC_ID,OPERATIONTYPE,OPERATIONVALUE,OPERATIONSUBVALUE,COMMENTS,OPERATOR,DATETIME)
        VALUES(SLA_FTTT_ESCALATION_OPS_id_seq.nextval,:ID,'UPDATED',:startdate,:sla_int_time,:enddate,:operator,sysdate)";
        // $lobjcon->Add_Row($query);
        $params = [
            ':ID' => $ID,
            ':startdate' => $startdate,
            ':sla_int_time' => $sla_int_time,
            ':enddate' => $enddate,
            ':operator' => $operator
        ];
        $result = $dbObject->execInsertUpdate($query, $params);

        $dat = 'success';
        echo json_encode($dat);
    } else {
        $dat = 'fail';
        echo json_encode($dat);
    }
} else if ($data_type == "GET_ESC_DATA") {
    $operator = $_SESSION['suuser1'];
    $allowedarray = array("salman.zia", "haris.salim", "mahnoor.mustajab", "mursleen.amjad", "saleha.saleem"); ////////// allowed personal to update time even after additin once

    $daterange = explode('-', $_POST['date']);
    $date1 = str_replace('/', '-', $daterange[0]);
    $date2 = str_replace('/', '-', $daterange[1]);
    $date1 = explode('-', $date1);
    $date2 = explode('-', $date2);

    $startdate = $date1[1] . '-' . $date1[0] . '-' . $date1[2];
    $startdate = date('d-M-Y', strtotime($startdate)) . ' 00:00:00';
    $enddate = $date2[1] . '-' . trim($date2[0]) . '-' . $date2[2];
    $enddate = date('d-M-Y', strtotime($enddate)) . ' 23:59:59';

    $query = "select ID,USERID,TTID,OUTAGE_TYPE,OPERATOR,EVENT_ID,USER_TYPE,DATETIME,ISSUE_REPORTED_BY,EMAIL_SUBJECT,OUTAGE_REASON,TO_CHAR(STARTTIME,'DD-MON-YYYY HH24:MI:SS')STARTTIME,TO_CHAR(SLA_INT_TIME,'DD-MON-YYYY HH24:MI:SS')SLA_INT_TIME,TO_CHAR(END_TIME,'DD-MON-YYYY HH24:MI:SS')END_TIME ,
    ROUND((END_TIME-SLA_INT_TIME)*24*60,1) TIME_SLA_INT,
    ROUND((END_TIME-STARTTIME)*24*60,1) TOTAL_TIME,
    DELAY_TIME,
    NOC_COMMENT,
    COMMENTS
    from SLA_FTTT_ESCLAIONS where DATETIME>=TO_DATE(:startdate,'DD-MON-YYYY HH24:MI:SS')and DATETIME<=TO_DATE(:enddate,'DD-MON-YYYY HH24:MI:SS')";
    // $res = $lobjcon->Get_Array($query);
    $params = [
        ':startdate' => $startdate,
        ':enddate' => $enddate
    ];
    $res = $dbObject->execSelect($query, $params);
    //var_dump($res);exit;
    // added a comments in above query by azeem
    //anum.rafaqa 
    //@date: 26-JUL-2022

    $event_ids = "select * from EVENTLOGGERFORM where status='ACTIVE'";
    $result_eventno =  $lobjcon->Get_Array($event_ids);

    //var_dump($result_eventno);exit;
    //$agregation1='<select class="form-control" name="event_id" id="event_id' . $res[$i]['ID'] .'">';
    $agregation2 = '<option value="">-- Please Select --</option>';
    for ($i = 0; $i < count($result_eventno); $i++) {
        $agregation2 .= '<option value="' . $result_eventno[$i]['EVENTNO'] . '">' . $result_eventno[$i]['EVENTNO'] . '</option>';
    }
    $agregation2 .= '</select>';
    for ($i = 0; $i < count($res); $i++) {

        //added by anum.rafaqat
        //@date: 25-jul-2022
        $result[$i][0] = $i + 1;
        $result[$i][1] = $res[$i]['DATETIME']; //ADDED BY ANUM


        $result[$i][2] = $res[$i]['EVENT_ID']; //ADDED BY ANUM
        //ADDED BY ANUM 
        $result[$i][3] = $res[$i]['TTID'];
        $result[$i][4] = $res[$i]['USERID'];
        $result[$i][5] = $res[$i]['OUTAGE_TYPE']; //ADDED BY ANUM
        $result[$i][6] = $res[$i]['ISSUE_REPORTED_BY']; //ADDED BY ANUM
        //ADDED BY ANUM
        $result[$i][7] = $res[$i]['EMAIL_SUBJECT']; //ADDED BY ANUM

        $result[$i][8] = $res[$i]['STARTTIME']; //ADDED BY ANUM
        $result[$i][9] =  $res[$i]['SLA_INT_TIME']; //ADDED BY ANUM
        $result[$i][10] = $res[$i]['END_TIME']; //ADDED BY ANUM
        $result[$i][11] = $res[$i]['TOTAL_TIME'];
        $result[$i][12] = $res[$i]['TIME_SLA_INT'];

        // <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        // <!-- Mursleen Amjad -->
        $result[$i][13] = $res[$i]['DELAY_TIME'];
        $result[$i][14] = $res[$i]['TIME_SLA_INT'] - $res[$i]['DELAY_TIME'];;
        $result[$i][15] = $res[$i]['NOC_COMMENT'];

        $outageHrs = round($result[$i][14] / 60);
        if ($outageHrs == 0) {
            $result[$i][16] = '< 1';
        } else {
            $result[$i][16] = '> ' . $outageHrs;
        }
        //actual downtime(telco - ) devided by 60 Delay
        // <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
        $result[$i][17] =  $res[$i]['OUTAGE_REASON'];
        //updated .. anum.rafaqat
        $result[$i][18] =    $res[$i]['OPERATOR'] . '(' . $res[$i]['DATETIME'] . ')';
        //updated .. anum.rafaqat
        if ($res[$i]['USER_TYPE'] == 'nonlegitimate') {
            $result[$i][19] =   '<p style="color:red"><b>UID Not found in CRM<b></p>';
        } else {
            $result[$i][19] =   '--';
        }

        $result[$i][20] = $res[$i]['COMMENTS']; //added by azeem


        // --------------------------------------------------------------
    }
    echo json_encode($result);
    exit;
} else if ($data_type == "GET_PENALTY_DATA") {

    $operator = $_SESSION['suuser1'];
    $daterange = explode('-', $_POST['date']);
    $date1 = str_replace('/', '-', $daterange[0]);
    $date2 = str_replace('/', '-', $daterange[1]);
    $date1 = explode('-', $date1);
    $date2 = explode('-', $date2);

    $startdate = $date1[1] . '-' . $date1[0] . '-' . $date1[2];
    $startdate = date('d-M-Y', strtotime($startdate)) . ' 00:00:00';
    $enddate = $date2[1] . '-' . trim($date2[0]) . '-' . $date2[2];
    $enddate = date('d-M-Y', strtotime($enddate)) . ' 23:59:59';
    ///////////////////////////////////////////////////////// get userids to map the logic required afad.amir/////////////////////////
    $query = "select userid  from mbluser where userid like 'jazzftts%' OR userid like 'tpagg%' OR userid like 'tptower%' OR userid like 'zongftts%'";
    $allUserIds = $lobjcon->Get_Array($query);

    $arr = array();
    $arr["tptower"] = array();
    $arr["tpagg"] = array();
    $arr["jazzftts"] = array();
    $arr["zongftts"] = array();

    foreach ($allUserIds as $k => $row) {
        $str = strpos($row["USERID"], "tptower") > -1
            ? "tptower" : (strpos($row["USERID"], "tpagg") > -1 ? "tpagg"  : (strpos($row["USERID"], "jazzftts") > -1 ? "jazzftts"  :  "zongftts")); // added by Raja Usman Raza on 13-SEP-2021
        $arr[$str][] =  $row[0];
    }

    //////////////////////////////////////////////////////////// fetching data for penalty for sla afad.amir/////////////////////////////

    $query = "select 
    sfe.USERID,sfe.OUTAGE_TYPE,COUNT(sfe.USERID)CELL_SITES,
    sum(ROUND((sfe.END_TIME-sfe.SLA_INT_TIME)*24*60,1)) TIME_SLA_INT,
    sum(ROUND((sfe.END_TIME-sfe.STARTTIME)*24*60,1)) TOTAL_TIME
    from SLA_FTTT_ESCLAIONS sfe  LEFT join FTTT_SLA_MAPPING fsm on fsm.agregationid=sfe.userid
    where sfe.DATETIME>=TO_DATE( :startdate,'DD-MON-YYYY HH24:MI:SS')and sfe.DATETIME<=TO_DATE(:enddate,'DD-MON-YYYY HH24:MI:SS') and sfe.starttime is not null
    GROUP BY sfe.USERID,sfe.OUTAGE_TYPE";
    // $res = $lobjcon->Get_Array($query);
    $params = [
        ':startdate' => $startdate,
        ':enddate' => $enddate
    ];
    $res = $dbObject->execSelect($query, $params);


    $result = array();
    $month_Min = 24 * 60 * 30;
    for ($i = 0; $i < count($res); $i++) {
        $result[$i][0] = $i + 1;
        $result[$i][1] = $res[$i]['USERID'];
        $result[$i][2] = $res[$i]['OUTAGE_TYPE'];

        $celsites = $res[$i]['CELL_SITES'];
        $sla_int_time = $res[$i]['TIME_SLA_INT'];
        $Total_TIME = $res[$i]['TOTAL_TIME'];

        if (in_array($res[$i]['USERID'], $arr["tpagg"])) {
            $result[$i][3] = round($res[$i]['TOTAL_TIME'] / $res[$i]['CELL_SITES'], 1);
            $result[$i][4] = round($res[$i]['TIME_SLA_INT'] / $res[$i]['CELL_SITES'], 1);

            $TotalUptime = round($month_Min - ($res[$i]['TOTAL_TIME'] / $res[$i]['CELL_SITES']), 1);
            $uptime_SLA_INT = round($month_Min - ($res[$i]['TIME_SLA_INT'] / $res[$i]['CELL_SITES']), 1);

            $result[$i][5] = $TotalUptime;
            $result[$i][6] = $uptime_SLA_INT;
            $adherance = round(($uptime_SLA_INT / $month_Min) * 100, 3);
            $result[$i][7] = $adherance;
            $result[$i][8] = $res[$i]['CELL_SITES'];

            if ($res[$i]['OUTAGE_TYPE'] == 'Single Leg') {
                $result[$i][9] = 0;
            } else if ($res[$i]['OUTAGE_TYPE'] == 'Both Legs') {
                if ($adherance >= 99.8) {
                    $result[$i][9] = "5%";
                } else if ($adherance < 99.8) {
                    $result[$i][9] = "10%";
                }
            }
        } else  if (in_array($res[$i]['USERID'], $arr["tptower"])) {

            $result[$i][3] = round($res[$i]['TOTAL_TIME'], 1);
            $result[$i][4] = round($res[$i]['TIME_SLA_INT'], 1);
            $TotalUptime = round($month_Min - $res[$i]['TOTAL_TIME'], 1);
            $uptime_SLA_INT = round($month_Min - $res[$i]['TIME_SLA_INT'], 1);

            $result[$i][5] = $TotalUptime;
            $result[$i][6] = $uptime_SLA_INT;
            $adherance = round(($uptime_SLA_INT / $month_Min) * 100, 3);
            $result[$i][7] = $adherance;
            $result[$i][8] = "-=-";
            if ($res[$i]['OUTAGE_TYPE'] == 'Single Leg') {
                $result[$i][9] = 0;
            } else if ($res[$i]['OUTAGE_TYPE'] == 'Both Legs') {
                if ($adherance >= 99.4) {
                    $result[$i][9] = "5%";
                } else if ($adherance < 99.4) {
                    $result[$i][9] = "10%";
                }
            }
            // Added by Raja Usman Raza on 13-sep-2021
            else  if (in_array($res[$i]['USERID'], $arr["zongftts"])) {

                $result[$i][3] = round($res[$i]['TOTAL_TIME'], 1);
                $result[$i][4] = round($res[$i]['TIME_SLA_INT'], 1);
                $TotalUptime = round($month_Min - $res[$i]['TOTAL_TIME'], 1);
                $uptime_SLA_INT = round($month_Min - $res[$i]['TIME_SLA_INT'], 1);

                $result[$i][5] = $TotalUptime;
                $result[$i][6] = $uptime_SLA_INT;
                $adherance = round(($uptime_SLA_INT / $month_Min) * 100, 3);
                $result[$i][7] = $adherance;
                $result[$i][8] = "-=-";
                if ($res[$i]['OUTAGE_TYPE'] == 'Single Leg') {
                    $result[$i][9] = 0;
                } else if ($res[$i]['OUTAGE_TYPE'] == 'Both Legs') {
                    if ($adherance >= 99.4) {
                        $result[$i][9] = "5%";
                    } else if ($adherance < 99.4) {
                        $result[$i][9] = "10%";
                    }
                }
                // Raja Usman Raza Code ends here 
            } else if (in_array($res[$i]['USERID'], $arr["jazzftts"])) {

                $result[$i][3] = round($res[$i]['TOTAL_TIME'], 1);
                $result[$i][4] = round($res[$i]['TIME_SLA_INT'], 1);
                $TotalUptime = round($month_Min - $res[$i]['TOTAL_TIME'], 1);
                $uptime_SLA_INT = round($month_Min - $res[$i]['TIME_SLA_INT'], 1);

                $result[$i][5] = $TotalUptime;
                $result[$i][6] = $uptime_SLA_INT;
                $adherance = round(($uptime_SLA_INT / $month_Min) * 100, 3);
                $result[$i][7] = $adherance;
                $result[$i][8] = "-=-";
                if ($res[$i]['OUTAGE_TYPE'] == 'Single Leg') {
                    $result[$i][9] = 0;
                } else if ($res[$i]['OUTAGE_TYPE'] == 'Both Legs') {
                    if ($adherance >= 99.4) {
                        $result[$i][9] = "5%";
                    } else if ($adherance < 99.4) {
                        $result[$i][9] = "10%";
                    }
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    //anum.rafaqat updated  25-aug-2022
} else if ($data_type == 'GET_OPEN_TICKETS') {

    // and td.operationtype='SLA CHECK' and operationvalue='active'
    $query = "select DISTINCT t.userid,CITY from NTLCRM.troubleticket t join mbluser mbl on mbl.userid=t.userid 
    join ntlcrm.troubleticketdetail td on t.id=td.ticketid 
    where t.close_time is null and t.userid like '%tptower%' or t.userid LIKE '%tpagg%' or t.userid LIKE '%jazzftts%' or t.userid LIKE '%zongftts%' order by t.userid";
    $res = $lobjcon->Get_Array($query);

    echo json_encode($res);
} else if ($data_type == 'GET_CLOSE_TICKETS') {

    $query = "select DISTINCT t.userid,t.ID from NTLCRM.troubleticket t join mbluser mbl on mbl.userid=t.userid 
    join ntlcrm.troubleticketdetail td on t.id=td.ticketid and td.operationtype='SLA CHECK' and operationvalue='inactive'
    where t.userid like '%tptower%' or t.userid LIKE '%tpagg%' or t.userid LIKE '%jazzftts%' or t.userid LIKE '%zongftts%' order by t.userid";
    $res = $lobjcon->Get_Array($query);
    //print_r($res);exit;
    echo json_encode($res);
} else if ($data_type == 'GET_EVENT_ID') {
    //echo "dfsd";
    $eventid = $_POST['eventid'];
    //echo $eventid;exit;
    $event_id = STRTOUPPER($eventid);
    if (strlen($eventid) > 2) {

        $query = "select * from EVENTLOGGERFORM where status='ACTIVE'  AND EVENTNO LIKE :event_id  AND ROWNUM <=5";
        // $res = $lobjcon->Get_Array($query);
        $params = [
            ':event_id' => '%' . $event_id . '%'
        ];
        $res = $dbObject->execSelect($query, $params);

        for ($i = 0; $i < count($res); $i++) {

            $data[$i] = $res[$i]['EVENTNO'];
        }
        echo json_encode($data);
    }
} else if ($data_type == 'CLOSE_TICKET') {

    $ID = $_POST['id'];
    $TTID = $_POST['TTID'];
    // echo $ID;
    // echo $TTID;exit;

    $operator = $_SESSION['suuser1'];
    $dept =  $_SESSION['SubDept'];

    // echo $operator;
    // echo $dept;exit;
    // --5274486
    $createComment = '(' . date('d-m-Y h:i:s') . ')/Ticket has been closed';

    if ($TTID != '') {
        $data = "select * from ntlcrm.troubleticket where id=:ttid";
        // $res = $lobjcon->Get_Array($data);
        $params = [
            ':ttid' => $TTID
        ];

        $res = $dbObject->execSelect($data, $params);

        //print_r(count($res));exit;
        if (count($res) >= 1) {
            $finProblem = "SELECT ID,OPERATIONTYPE, OPERATIONVALUE FROM NTLCRM.TROUBLETICKETDETAIL WHERE ID IN (SELECT MAX(ID) FROM NTLCRM.TROUBLETICKETDETAIL WHERE  TICKETID = :ttid AND OPERATIONTYPE ='FIN PROBLEM' )";
            // $resProblem  = $lobjcon->Get_Array($finProblem);
            $params = [
                ':ttid' => $TTID
            ];
            $resProblem = $dbObject->execSelect($finProblem, $params);

            // echo $resProblem[0]['OPERATIONVALUE'];echo "next avl";

            $finSolution = "SELECT ID,OPERATIONTYPE, OPERATIONVALUE FROM NTLCRM.TROUBLETICKETDETAIL WHERE ID IN (SELECT MAX(ID) FROM NTLCRM.TROUBLETICKETDETAIL WHERE  TICKETID = :ttid AND OPERATIONTYPE ='FIN SOLUTION' ) ";
            // $resSolution  = $lobjcon->Get_Array($finSolution);
            $params = [
                ':ttid' => $TTID
            ];
            $resSolution = $dbObject->execSelect($finSolution, $params);

            // echo $resSolution[0]['OPERATIONVALUE'];exit;

            if ($resProblem[0]['OPERATIONVALUE'] != "" && $resSolution[0]['OPERATIONVALUE'] != "") {
                //  echo "dfvd";exit;
                // $update = "update ntlcrm.troubleticket set close_time=sysdate where ID = '$TTID'";
                $update = "update ntlcrm.troubleticket set close_time=sysdate where ID = :TTID";
                // echo $update;
                $params = [
                    ':TTID' => $TTID
                ];
                //print_r($lobjcon->Add_Row($update));exit;
                if ($dbObject->execInsertUpdate($update, $params)) {
                    //echo "dsfgsd";exit;
                    $addDetails = $newttObj->addTTDetails(
                        $TTID,
                        'CLOSE TT',
                        $res[0]['FAULTTYPE'],
                        $res[0]['SUBFAULTTYPE'],
                        '',
                        '',
                        $operator,
                        $createComment,
                        '',
                        '',
                        '',
                        'closed',
                        $dept,
                        ''
                    );
                    echo json_encode('Close');
                }
            } else {
                //echo "bye";exit;
                echo json_encode('NOSOLUTION');
            }
        } else {
            echo json_encode('error');
        }
    } else {
        echo json_encode('error');
    }
}
//anum.rafaqat updated  25-aug-2022

//updated by anum.rafaqat
//@date:: 26-08-2022
function make_select($id, $selected_val, $rowid)
{

    $agregation = '<input name="event_id" id="event_id' .  $rowid . '"  onfocus="getEventID(\'event_id' .  $rowid . '\')" class="form-control" value="' . $selected_val . '" size="10" attr="true" required>';
    return $agregation;
    // <input type='text' style='margin-top: 10px;'  onfocus='getcrmid(\"fwdTo_".$report[$index]['MAININSTALLID']."\")' id='fwdTo_".$report[$index]['MAININSTALLID']."' size='10' attr='".$attr."'  name='forwardToID' value='".$report[$index]['FORWARDTO']."'>
    //alert("sdsf"); $agregation2 = '<option value="">-- Please Select --</option>';
    //     $lobjcon = new DbClass1();
    // //var_dump($id);
    // //var_dump($selected_val);
    //     if($id == 'event_id'){
    //         if($selected_val != ''){
    //        // var_dump("sadsad");

    //         //updated by anum.rafaqat @date:24-aug-2022
    //     $event_ids ="select * from EVENTLOGGERFORM where status='ACTIVE' order by id desc";
    //      //updated by anum.rafaqat @date:24-aug-2022
    //     $result_eventno =  $lobjcon->Get_Array($event_ids);

    // //print_r($result_eventno);
    //     $agregation = '<select name="event_id" id="event_id' .  $rowid . '" class="form-control search-select" required>';
    //     $agregation .= '<option value="">--Please Select--</option>';
    //      for ($j = 0; $j < count($result_eventno); $j++) {
    //          $agregation .= '<option value="' . $result_eventno[$j]['EVENTNO'];

    //         if ($result_eventno[$j]['EVENTNO'] == $selected_val) {
    //             $agregation .= '" selected  >' . $result_eventno[$j]['EVENTNO'] . '</option>';
    //         } else {
    //             $agregation .= '" >' . $result_eventno[$j]['EVENTNO'] . '</option>';
    //         }
    //     }
    //     $agregation .= '</select>';
    //     // echo '<pre>';
    //     // var_dump($agregation );
    //     // echo '</pre>';
    //     // exit;
    //     return $agregation;
    //}else{
    // var_dump("sadsad");

    //      //updated by anum.rafaqat @date:24-aug-2022
    //      $event_ids ="select * from EVENTLOGGERFORM where status='ACTIVE' order by id desc";
    //     //updated by anum.rafaqat @date:24-aug-2022
    //      $result_eventno =  $lobjcon->Get_Array($event_ids);

    //  //print_r($result_eventno);
    //      $agregation = '<select name="event_id" id="event_id' . $rowid . '" class="form-control search-select"  required>';
    //      $agregation .= '<option value="">--Please Select--</option>';
    //       for ($j = 0; $j < count($result_eventno); $j++) {
    //           $agregation .= '<option value="' . $result_eventno[$j]['EVENTNO'];
    //              $agregation .= '" >' . $result_eventno[$j]['EVENTNO'] . '</option>';
    //      }
    //      $agregation .= '</select>';
    //     //  echo '<pre>';
    //     //  var_dump($agregation );
    //     //  echo '</pre>';
    //     //  exit;
    //      return $agregation;

    //}
}
//}

//@date:: 26-08-2022
