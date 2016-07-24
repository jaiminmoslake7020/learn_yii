<?php

/****
 * Created by PhpStorm.
 * User: Swing
 * Date: 1/8/2016
 * Time: 2:37 PM
 ****/

namespace common\components;

use common\models\User;
use frontend\controllers\SiteController;
use common\models\Assignments;
use common\models\ClientDatabases;
use common\models\CoreReports;
use common\models\Criteria;
use common\models\CriteriaLinks;
use common\models\DatabaseType;
use common\models\DbCharacterEncoding;
use common\models\DisplayDateFormat;
use common\models\DisplayOrder;
use common\models\Graph;
use common\models\Group;
use common\models\GroupHeader;
use common\models\GroupTrailer;
use common\models\IndustryDatabase;
use common\models\Languages;
use common\models\OrderColumn;
use common\models\OutputCharacterEncoding;
use common\models\PageHeaderOrFooter;
use common\models\Plot;
use common\models\PreSql;
use common\models\QueryColumn;
use yii\base\Exception;
use yii\db\Connection;
use common\models\QueryDb;
use common\models\DataSource;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;
use common\components\FrontEndHelper;

/**
 * Class AppBasic Provides basic function to make programming EASY and FUN things
 * Author : Jaimin MosLake
 **/


class AppBasic
{

    public static function renderSuccess($message)
    {
        $data = array();
        $data['status'] = 1;
        $data['action'] = ["NOTHING"];
        $data['message'] = $message;

        echo json_encode($data);
        exit;
    }


    public static function rederJsonErrors($model, $exit = 1)
    {
        $explodeArray = explode("\\", $model->className() );
        if(!empty($explodeArray))
        {
            $className = $explodeArray[sizeof($explodeArray)-1];
        }
        else
        {
            $className = $model->className();
        }

        $data = array();
        $data['status'] = 0;
        $data['errorJson'] = $model->getErrors();
        $data['modelName'] = $className;


        if(\Yii::$app->request->isAjax)
        {
            //AppBasic::ca
            echo json_encode($data);

            if($exit)
            {
                exit;
            }
        }
        else
        {
            $errors = $model->getErrors() ;
            $message = AppBasic::getErrorMessageStraight($errors);
            throw new HttpException(431, $message );
        }

    }




    /*
           * I use this function to handle auto complete of password
           * I put this in start of login page so chrome/ other browsers do not auto complete the username and password.
           */
    public static function renderAutoCompleteOff()
    {
        return ' 
        <input type="text" style="visibility: hidden;height:0px;" />
        <input type="password" style="visibility: hidden;height:0px;"  /> 
        ';
    }

    public static function formOverlay()
    {
        /*
        return ' 
        <div class="overlay" >
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        ';
        */
    }

    public static function arrayKeyExist($key, $array , $returnValue = 1, $returnArray =  0)
    {
        if($returnValue)
        {
            return is_array($array) ?  array_key_exists($key , $array) ? $array[$key] : ( $returnArray ? [] : null )  : ( $returnArray ? [] : null ) ;
        }
        else
        {
            return is_array($array) ?  array_key_exists($key , $array) ? true : false : false ;
        }
    }

    public static function arrayNotEmpty( $array )
    {
        return is_array($array) ? !empty($array) ? true : false : false ;
    }

    public static function propertyExist($key, $object , $returnValue = 1)
    {
        if($returnValue)
        {
            return is_object($object) ?  property_exists($object , $key) ? $object->$key : null : null ;
        }
        else
        {
            return is_object($object) ?  property_exists($object , $key) ? 1 : 0 : 0 ;
        }
    }

    public static function isSquential($arr)
    {
        return  is_array($arr) ? array_keys($arr) === range(0, count($arr) - 1) : 0;
    }

    public static function makeItSquential($arr)
    {
        return (!empty($arr)) ? (self::isSquential($arr) ? $arr : [$arr]) : [] ;
    }

    public static function stringNotNull($string)
    {
        return ($string != "" && $string != null);
    }

    public static function stringNull($string)
    {
        return !self::stringNotNull($string);
    }

    public static function giveDetail($data)
    {
        return (is_object($data) ? "OBJECT" : (is_array($data) ?  (AppBasic::isSquential($data) ? "SEQUENTIAL_ARRAY" :  "ASSOCIATIVE_ARRAY" )  : "STRING" )) ;
    }

    public static function giveDetailP($data , $exit = 0)
    {
        return self::printR(  self::giveDetail($data)  , $exit);
    }

    public static function printR($data, $exit = 1)
    {
        echo "<pre></br>";
        print_r($data);
        if ($exit == '1') {
            exit;
        }

        echo "</pre></br>";
    }

    public static function printRT($data, $title = null , $exit = 0)
    {
         AppBasic::printR($title." START ", 0);
         AppBasic::printR($data, 0 );
         AppBasic::printR($title." END ", $exit);
    }


    public static function test($exit = 0 , $file = __FILE__, $class = __CLASS__ , $function = __FUNCTION__, $line = __LINE__ )
    {
        self::printR(" FILE : ".$file." <br/> CLASS : ".$class." <BR/> FUNCTION : ".$function." <BR/> LINE : ".$line, $exit);
    }

    public static function printReturn($data, $status = 1)
    {
        $html = "" ;
        $html .= "<pre></br>";
        $html .=  print_r($data , true );
        $html .= "</pre></br>";

        return $html ;
    }

    public static function getErrorArray($msg, $errors)
    {

        foreach ($errors as $k => $value)
        {
            for ($i = 0; $i < sizeof($value); $i++)
            {
                $msg[sizeof($msg)] = $value[$i];
            }
        }

        return $msg;
    }

    public static function getErrorArrayStraight($msg, $errors)
    {

        foreach ($msg as $k => $value) {
            $p = array_key_exists($k, $errors) ? sizeof($errors[$k]) : 0;
            $errors[$k][$p] = $value;
        }

        return $errors;
    }

    public static function getFinalError($array1, $array2)
    {
        $error = Helpers::getErrorArrayStraight($array1, $array2);
        $message = Helpers::getErrorMessageStraight($error);

        return $message;
    }

    public static function getErrorMessageStraight($errors)
    {

        $html = "";
        $html .= "<p>Please solve the following errors</p>";
        $html .= "<ul>";
        foreach ($errors as $k => $value) {
            for ($i = 0; $i < sizeof($value); $i++) {
                $html .= "<li>";
                $html .= $value[$i];
                $html .= "</li>";
            }
        }
        $html .= "</ul>";

        return $html;
    }

    public static function getErrorMessage($type, $msg)
    {
        $html = "";
        if ($type == "failed") {
            $html .= '<div class="whistleblower" >--__FAILED__--</div>';
            $html .= '<div class="errors"><ul id="errors">';
            for ($i = 0; $i < sizeof($msg); $i++) {
                $html .= '<li>' . $msg[$i] . '</li>';
            }
            $html .= '</ul></div>';
        } else {
            $html .= '<div class="whistleblower" >--__SUCCESS__--</div>';
            $html .= '<div class="errors"><ul id="errors">';
            for ($i = 0; $i < sizeof($msg); $i++) {
                $html .= '<li>' . $msg[$i] . '</li>';
            }
            $html .= '</ul></div>';
        }
        return $html;
    }

    public static function getUrlInfo($name , $operator = "/")
    {
        $controllerName = \Yii::$app->controller->id ;
        $controllerName = strtolower($controllerName) ;
        $actionName =  \Yii::$app->controller->action->id ;
        $actionName = strtolower($actionName) ;
        $combination = $controllerName.$operator.$actionName ;

        return $$name ;
    }

    public static function date_convert($dt, $tz1, $df1 = "Y-m-d H:i:s", $tz2 = "UTC", $df2 = "Y-m-d H:i:s")
    {
        $returnVal = null ;
        if($dt != null || $dt != "")
        {
            if($tz1 == null || $tz1 == "")
            {
                $tz1 = date_default_timezone_get();
            }

            if($tz2 == null || $tz2 == "")
            {
                $tz2 = "UTC";
            }

            $timeZoneObj = new \DateTimeZone($tz1);
            //create DateTime object
            $d = \DateTime::createFromFormat($df1, $dt, $timeZoneObj );
            //convert timezone
            if(is_object($d))
            {

                $timeZoneObj2 = new \DateTimeZone($tz2);

                try
                {
                    $d->setTimeZone($timeZoneObj2);
                }
                catch(Exception $e)
                {
//                Helpers::printR("dt" , 0);
//                Helpers::printR($dt , 0);
//
//                Helpers::printR("tz1" , 0);
//                Helpers::printR($tz1 , 0);
//
//                Helpers::printR("df1" , 0);
//                Helpers::printR($df1);
                }



            }
            //convert dateformat
            $returnVal = is_object($d) ? $d->format($df2) : null  ;
        }

        return  $returnVal ;
    }

    public static function checkValidations($model, $exit = 1)
    {

        self::printR('Attribute', 0);
        self::printR($model->scenario, 0);

        self::printR('Attribute', 0);
        self::printR($model->attributes, 0);

        self::printR('Validate', 0);
        self::printR($model->validate(), 0);

        self::printR('Errors', 0);
        self::printR($model->getErrors(), 0);

        //self::printR('Model', 0);
        //self::printR($model, 0);

        if ($exit) {
            exit;
        }

    }

    public static function setTimeZoneByTimezone($tz)
    {
        if($tz != null && $tz != "")
        {

            //Helpers::printR($tz );
            ini_set('date.timezone', $tz);
            //echo date_default_timezone_get();
            //exit;
        }
    }

    public static function getBodyClass()
    {
        $combination = AppBasic::getUrlInfo('combination');
        return $combination == "site/index" ? "loginPage" : "mainPage" ;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function getExtention($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return $ext;
    }

    public static function createDirectoryStructureFromBasePath($structure)
    {
        $url = Yii::app()->basePath;
        $explode = explode("/", $structure);
        //self::printR($explode , 0 );

        foreach($explode as $k=>$val)
        {
            if($val == "..")
            {
                $dir = $url."/".$val;
                if(file_exists($dir) && is_dir($dir))
                {
                    $url = $dir ;
                }
                else
                {
                    $url = $url ;
                }
            }
            else if($val != "")
            {
                $dir = $url.'/'.$val;
                if (!file_exists($dir) && !is_dir($dir)) {
                    mkdir($dir);
                }

                $url = $dir ;
            }
        }


        return $url."/" ;
        //self::printR($url);
        //$file = $dir."/".$fileNameShould;
    }

    public static function getWebsiteSetting($viewName , $createModule = null ,$name)
    {

//        AppBasic::printRT($viewName , 'viewName');
//        AppBasic::printRT($createModule , 'createModule');
//        AppBasic::printRT($name , 'name', 1);

        $row = array() ;

        if(self::stringNotNull($viewName) && self::stringNotNull($createModule))
        {
            $sql = '
                 SELECT `value` FROM `website_settings`
                  INNER JOIN `view` ON `view`.`view_id` = `website_settings`.`view_id`
                  INNER JOIN `create_module` ON `create_module`.`create_module_id` = `website_settings`.`create_module_id`
                  WHERE `website_settings`.`name`=:name AND `view`.`name`=:viewName AND `create_module`.`report`=:report
            ';

//            $query  = phone Query();
//            $query->createCommand(\Yii::$app->db);
//            $command = $query->createCommand($sql);
//            $command->bindParam(':name',$name);
//            $command->bindParam(':viewName',$viewName);
//            $command->bindParam(':report',$createModule);
//            $row = $command->queryOne();

//            $query  = phone Connection();
//            $command = $query->createCommand($sql);
//            $command->bindParam(':name',$name);
//            $command->bindParam(':viewName',$viewName);
//            $command->bindParam(':report',$createModule);
//            $command->db =  Yii::$app->db ;
//            $row = $command->queryOne();


            $Query =  new Connection();
            $command = $Query->createCommand($sql);
            $command->db = \Yii::$app->db ;
            $command->bindParam(':name',$name);
            $command->bindParam(':viewName',$viewName);
            $command->bindParam(':report',$createModule);
            $row = $command->queryOne();


//            $query  = phone Connection();
//            $command = $query->createCommand($sql);
//            $command->bindParam(':name',$name);
//            $command->bindParam(':viewName',$viewName);
//            $command->bindParam(':report',$createModule);
//            $command->db =  Yii::$app->db ;
//            $row = $command->queryOne();


        }
        else if(self::stringNotNull($viewName))
        {
            $sql = '
                 SELECT `value` FROM `website_settings`
                  INNER JOIN `view` ON `view`.`view_id` = `website_settings`.`view_id`
                  WHERE `website_settings`.`name`=:name AND `view`.`name`=:viewName
            ';


            $query  = new Connection();
            $command = $query->createCommand($sql);
            $command->db =  \Yii::$app->db ;
            $command->bindParam(':name',$name);
            $command->bindParam(':viewName',$viewName);
            $row = $command->queryOne();



        }
        else
        {
            $sql = '
                 SELECT `value` FROM `website_settings` WHERE `name`=:name
            ';

//            $query  = phone Query();
//            $command = $query->createCommand($sql);
//            $command->bindParam(':name',$name);
//            $row = $command->queryRow();

            $query  = new Connection();
            $command = $query->createCommand($sql);
            $command->db =  \Yii::$app->db ;
            $command->bindParam(':name',$name);
            $row = $command->queryOne();

        }



        return self::arrayKeyExist('value',$row) ;

    }

    public static function convertEmptyArrayToEmptyString($array)
    {
        $newArray = array() ;
        if(is_array($array))
        {
            foreach($array as $k=>$val)
            {
                if(is_array($val))
                {
                    if(empty($val))
                    {
                        $newArray[$k] = null ;
                    }
                    else
                    {
                        $newArray[$k] = self::convertEmptyArrayToEmptyString($val);
                    }
                }
                else
                {
                    $newArray[$k] = $val ;
                }
            }
        }
        return $newArray;
    }

    public static function handle_user_entry($data, $report_name )
    {
        $xml = simplexml_load_string((string)$data, "SimpleXMLElement", LIBXML_NOCDATA) ;
        $json = json_encode($xml) ;
        $array = json_decode($json,TRUE) ;
        $array = self::convertEmptyArrayToEmptyString($array) ;

        $formatArray = self::arrayKeyExist('ReportQuery',$array) ;
        if(self::arrayKeyExist('Format',$formatArray))
        {

            $core_report_id = self::arrayKeyExist('CoreReportId',$formatArray['Format']);
            //self::printR("core_report_id" , 0);


            if($core_report_id != "" && $core_report_id != null)
            {
                $coreReports =  CoreReports::findOne($core_report_id);
                $coreReports->setScenario('create');
            }
            else
            {
                $coreReports = new CoreReports();
                $coreReports->setScenario('create');
            }

            //self::printR($formatArray['Format'] , 0);
            $Format = self::arrayKeyExist('Format', $formatArray) ;

            if(!empty($Format))
            {

                //AppBasic::printR($coreReports->attributes , 0);

                $clientSpecific = $coreReports->is_client_specific ;

                $coreReports->setAttributes($Format);
                $coreReports->report_name = $report_name ;

                $coreReports->is_client_specific = $clientSpecific;
                //$coreReports->report_type =  self::arrayKeyExist( 'ReportType' , $Format) ;
                //$coreReports->is_client_specific = $coreReports->is_client_specific == "YES" ? 1 : 0 ;
                //$coreReports->industry_database_id = 1 ;
                //AppBasic::printR($coreReports->attributes);

                if(!$coreReports->save())
                {
                    $html = AppBasic::getErrorMessageStraight($coreReports->getErrors());
                    trigger_error($html);
                }
                else
                {
                    $dataSourceId = null ;
                    $QueryReturnArray = null ;
                    $criteriaItemArray = null ;

                    $core_report_id = $coreReports->core_report_id ;
                    \Yii::$app->session->set('created_core_report_id', $core_report_id);
                    $dataSourceId = self::createUpdateDataSource($array, $core_report_id );
                    $QueryReturnArray = self::createUpdateQueries($array, $core_report_id );
                    $assignmentsArray = self::createUpdateAssignments($array, $core_report_id);
                    $criteriaItemArray = self::createUpdateCriterias($array, $core_report_id);
                    $outputTabArray = self::handleOutputTab($array, $core_report_id);

                    $idsArray = array();
                    $idsArray['attributes']['CoreReportId'] = $core_report_id ;
                    if(self::stringNotNull($dataSourceId))
                    {
                        $idsArray['DataSourceId'] = $dataSourceId ;
                    }

                    if(is_array($QueryReturnArray))
                    {
                        $QueryId = self::arrayKeyExist('QueryId', $QueryReturnArray);
                        $QueryColumns = self::arrayKeyExist('QueryColumns', $QueryReturnArray);
                        $PreSQLS = self::arrayKeyExist('pre_sql', $QueryReturnArray);
                        if(self::stringNotNull($QueryId))
                        {
                            $idsArray['columns']['QueryColumnId'] = $QueryColumns ;
                            $idsArray['pre_sql']['PreSqlId'] = $PreSQLS ;
                            $idsArray['QueryId'] = $QueryId ;
                        }
                    }

                    if(is_array($assignmentsArray))
                    {
                        $idsArray['assignment']['assignment_id'] = $assignmentsArray ;
                    }

                    if(is_array($criteriaItemArray))
                    {
                        $idsArray['lookup_queries'] = $criteriaItemArray ;
                    }

                    if(is_array($outputTabArray))
                    {
                        foreach($outputTabArray as $k=>$val)
                        {
                            $idsArray[$k] = $val ;
                        }
                    }


                    //self::printR($idsArray , 0);
                    return $idsArray ;

                }

            }
            else
            {
                trigger_error(self::listOfExceptions(1001));
            }


         }
         else
         {
             trigger_error(self::listOfExceptions(1001));
         }

         return null ;
    }

    public static function createUpdateDataSource($data, $core_report_id)
    {
        $ReportQuery = self::arrayKeyExist('ReportQuery',$data) ;
        $formatArray = self::arrayKeyExist('Format',$ReportQuery) ;
        $DatasourceArray = self::arrayKeyExist('Datasource',$ReportQuery) ;

        if(self::arrayKeyExist('Datasource',$ReportQuery, 0))
        {

            //self::printR($DatasourceArray , 0);
            $data_source_id= self::arrayKeyExist('DataSourceId',$DatasourceArray);
            if($data_source_id!= "" && $data_source_id!= null)
            {
                $DataSource =  DataSource::findOne($data_source_id);
            }
            else
            {
                $DataSource = new DataSource();
            }


            //self::printR($DatasourceArray , 0);
            $DataSource->setAttributes( $DatasourceArray );
            $SourceConnection = self::arrayKeyExist('SourceConnection', $DatasourceArray );
            $DataSource->setAttributes( $SourceConnection );
            $DataSource->core_report_id = $core_report_id ;
            //self::printR($DataSource->attributes , 0);

            if(!$DataSource->save())
            {
                $html = AppBasic::getErrorMessageStraight($DataSource->getErrors());
                trigger_error($html);
            }
            else
            {
                $dataSourceId = $DataSource->data_source_id ;

                if(self::stringNotNull($dataSourceId))
                {
                    $sql = '
                            DELETE FROM `data_source`
                                 WHERE
                                      `core_report_id`=:core_report_id
                                 AND  `data_source_id` <> :data_source_id
                    ';

                    $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':data_source_id'=>$dataSourceId
                        ));

                    $return = $conn->execute();
                }

                return $dataSourceId;
            }

        }
        else
        {
            trigger_error(self::listOfExceptions(1002));
        }
    }

    public static function createUpdateQueries($data, $core_report_id = null, $criteria_id = null)
    {

        if($core_report_id != null && $core_report_id != "")
        {
            $ReportQuery = self::arrayKeyExist('ReportQuery',$data) ;
            $formatArray = self::arrayKeyExist('Format',$ReportQuery) ;
            $EntryForm = self::arrayKeyExist('EntryForm',$ReportQuery) ;
            $QueryArray = self::arrayKeyExist('Query',$EntryForm) ;
        }
        else if($criteria_id != null && $criteria_id != "" )
        {
            $EntryForm = $data ;
            $QueryArray = self::arrayKeyExist('Query',$EntryForm) ;
        }



        //self::printRT($core_report_id, 'core_report_id');
        //self::printRT($criteria_id, 'criteria_id');
        //self::printR($EntryForm);



        if(self::arrayKeyExist('Query',$EntryForm , 0))
        {
            //self::printR($QueryArray , 0);
            $query_id = self::arrayKeyExist('QueryId',$QueryArray);
            if($query_id != "" && $query_id != null)
            {
                $Query =  QueryDb::findOne($query_id);
            }
            else
            {
                $Query = new QueryDb();
            }

            $Query->setAttributes($QueryArray);
            $SQL = self::arrayKeyExist('SQL',$QueryArray);
            $Query->setAttributes( $SQL );
            $Query->core_report_id = $core_report_id ;
            $Query->criteria_id = $criteria_id ;

            if(!$Query->save())
            {
                $html = AppBasic::getErrorMessageStraight($Query->getErrors());
                trigger_error($html);
            }
            else
            {
                $QueryId = $Query->query_id;

                $assignmentsIdString = "" ;
                $QueryColumnIdString = "" ;
                $PreSqlIdString = "" ;
                $OrderColumnIdString = "" ;


                // Handling QueryColumns
                $QueryColumnIdArray = AppBasic::handleQueryColumns($QueryId, $QueryArray);

                // Handling PreSqls  It will only available when there is mainquerquery form
                if($core_report_id != null && $core_report_id != "")
                {
                    $PreSqlIdArray = AppBasic::handlePreSqls($QueryId, $QueryArray);
                }

                // Handling Assignments  It will only available when there is criteria form
                if($criteria_id != null && $criteria_id != "")
                {
                    $assignmentsIdArray = AppBasic::createUpdateAssignments($QueryArray, null, $QueryId);
                }

                // Handling OrederColumn
                $OrderColumnIdArray = AppBasic::handleOrderColumns($QueryId, $QueryArray);



                $queryReturnArray = array();
                $queryReturnArray['QueryId'] = $QueryId;
                if($core_report_id != null && $core_report_id != "")
                {
                    $queryReturnArray['pre_sql'] = $PreSqlIdArray ;
                }
                if($criteria_id != null && $criteria_id != "")
                {
                    $queryReturnArray['assignment'] = $assignmentsIdArray ;
                }
                if(!empty($QueryColumnIdArray))
                {
                    $queryReturnArray['QueryColumns'] = $QueryColumnIdArray;
                }
                if(!empty($OrderColumnIdArray))
                {
                    $queryReturnArray['order_set'] = $OrderColumnIdArray;
                }


                    $sql = '
                            DELETE pre_sql FROM `pre_sql`
                                 INNER JOIN `query` ON `query`.`query_id` = `pre_sql`.`query_id`
                                 WHERE     `query`.`core_report_id` = :core_report_id
                                       AND `query`.`query_id` <> :query_id
                    ';

                    $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':query_id'=>$QueryId
                        )
                    );

                   $return = $conn->execute();

                   $sql = '
                            DELETE assignments FROM `assignments`
                                 INNER JOIN `query` ON `query`.`query_id` = `assignments`.`query_id`
                                 WHERE     `query`.`core_report_id` = :core_report_id
                                       AND `query`.`query_id` <> :query_id
                   ';

                   $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':query_id'=>$QueryId
                        )
                   );

                    $return = $conn->execute();

                    $sql = '
                            DELETE query_column FROM `query_column`
                                 INNER JOIN `query` ON `query`.`query_id` = `query_column`.`query_id`
                                 WHERE     `query`.`core_report_id` = :core_report_id
                                       AND `query`.`query_id` <> :query_id
                    ';

                    $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':query_id'=>$QueryId
                        )
                    );

                    $return = $conn->execute();

                    $sql = '
                                DELETE order_column FROM `order_column`
                                     INNER JOIN `query` ON `query`.`query_id` = `order_column`.`query_id`
                                     WHERE     `query`.`core_report_id` = :core_report_id
                                           AND `query`.`query_id` <> :query_id
                        ';

                    $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':query_id'=>$QueryId
                        )
                    );

                    $return = $conn->execute();

                    $sql = '
                        DELETE `query`
                          FROM `query`
                         WHERE     `query`.`core_report_id` = :core_report_id
                               AND `query`.`query_id` <> :query_id
                    ';

                    $conn = \Yii::$app->db->createCommand($sql,
                        array(
                            ':core_report_id'=>$core_report_id,
                            ':query_id'=>$QueryId
                        )
                    );

                    $return = $conn->execute();




                    return $queryReturnArray;


            }
        }
        //else
        //{
        //  trigger_error(self::listOfExceptions(1003));
        //}

    }

    public static function handleQueryColumns($QueryId , $QueryArray)
    {
        $QueryColumnIdArray = array();
        $QueryColumns = self::arrayKeyExist('QueryColumns',$QueryArray);
        $QueryColumn = self::arrayKeyExist('QueryColumn',$QueryColumns);
        $QueryColumnIdString = "" ;

        if(!AppBasic::isSquential($QueryColumn))
        {
            $QueryColumnN = array();
            $QueryColumnN[] = $QueryColumn;
            $QueryColumn = $QueryColumnN ;
        }

        for($i= 0;  $i <  sizeof($QueryColumn); $i++)
        {
            $QueryColumnIdPrev = self::arrayKeyExist('QueryColumnId' , $QueryColumn[$i] );
            $QueryColumnId = self::createUpdateQueryColumn($QueryColumn[$i], $QueryId, $QueryColumnIdPrev );
            if(self::stringNotNull($QueryColumnId))
            {
                $QueryColumnIdArray[] = $QueryColumnId ;
                $QueryColumnIdString .= '"'.$QueryColumnId.'",';
            }
        }



        $sql = '

                    DELETE FROM `query_column`
                         WHERE
                              `query_id` = :query_id

                    ';

        $QueryColumnIdString = trim($QueryColumnIdString, ",");
        if($QueryColumnIdString != "")
        {
            $sql .= '
                          AND `query_column_id` NOT IN ('.$QueryColumnIdString.');
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':query_id'=>$QueryId ));
        $return = $conn->execute();


        return $QueryColumnIdArray ;
    }

    public static function createUpdateQueryColumn($data, $QueryId, $QueryColumnId = null)
    {

        if($QueryColumnId != "" && $QueryColumnId!= null)
        {
            $QueryColumn = QueryColumn::findOne($QueryColumnId);
        }
        else
        {
            $QueryColumn = new QueryColumn();
        }


        $QueryColumn->setAttributes($data);
        $Format = self::arrayKeyExist('Format',$data);
        $QueryColumn->setAttributes($Format);
        $QueryColumn->query_id = $QueryId ;
        //self::printR($QueryColumn->attributes , 0);

        if(!$QueryColumn->save())
        {
            $html = AppBasic::getErrorMessageStraight($QueryColumn->getErrors());
            trigger_error($html);
        }
        else
        {
            //self::printR($QueryColumn->query_column_id);
            return $QueryColumn->query_column_id;
        }

    }

    public static function handlePreSqls($QueryId , $QueryArray)
    {
        $PreSqlIdArray = array();
        $PreSqls = self::arrayKeyExist('PreSQLS',$QueryArray);
        $PreSql = self::arrayKeyExist('PreSQL',$PreSqls);
        $PreSqlIdString = "" ;

        if(!AppBasic::isSquential($PreSql))
        {
            $PreSqlN = array();
            $PreSqlN[] = $PreSql;
            $PreSql = $PreSqlN ;
        }

        for($i = 0 ;  $i < sizeof($PreSql); $i++)
        {
            $PreSqlIdPrev = self::arrayKeyExist('PreSqlId' ,$PreSql[$i]);
            $PreSqlId = self::createUpdatePreSql($PreSql[$i], $QueryId, $PreSqlIdPrev);
            if(self::stringNotNull($PreSqlId))
            {
                $PreSqlIdArray[] = $PreSqlId ;
                $PreSqlIdString .= '"'.$PreSqlId.'",';
            }
        }

        $PreSqlIdString = trim($PreSqlIdString, ",");
        $sql = '
                    DELETE FROM `pre_sql`
                         WHERE
                              `query_id` = :query_id

                    ';

        if($PreSqlIdString != "")
        {
            $sql .= '
                          AND `pre_sql_id` NOT IN ('.$PreSqlIdString.');
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':query_id'=>$QueryId ));
        $return = $conn->execute();


        return $PreSqlIdArray ;

    }

    public static function createUpdatePreSql($data, $QueryId, $PreSqlId = null)
    {
        if(self::arrayNotEmpty($data))
        {

            if ($PreSqlId != "" && $PreSqlId != null) {
                $PreSql = PreSql::findOne($PreSqlId);
            } else {
                $PreSql = new PreSql();
            }

            $PreSql->setAttributes($data);
            $PreSql->query_id = $QueryId;
            //self::printR($PreSql->attributes );

            if (!$PreSql->save()) {
                $html = AppBasic::getErrorMessageStraight($PreSql->getErrors());
                trigger_error($html);
            }else{
                //self::printR($PreSql->query_column_id);
                return $PreSql->pre_sql_id;
            }

        }
    }

    public static function handleOrderColumns($QueryId , $QueryArray)
    {
        $OrderColumnIdArray = array();
        $OrderColumns = self::arrayKeyExist('OrderColumns',$QueryArray);
        $OrderColumn = self::arrayKeyExist('OrderColumn',$OrderColumns);
        $OrderColumnIdString = "" ;

        if(!AppBasic::isSquential($OrderColumn))
        {
            $OrderColumnN = array();
            $OrderColumnN[] = $OrderColumn;
            $OrderColumn = $OrderColumnN ;
        }

        for($i = 0 ;  $i <  sizeof($OrderColumn); $i++)
        {
            $OrderColumnIdPrev = self::arrayKeyExist('OrderColumnId' , $OrderColumn[$i]);
            $OrderColumnId = self::createUpdateOrderColumn($OrderColumn[$i], $QueryId, $OrderColumnIdPrev);
            if(self::stringNotNull($OrderColumnId))
            {
                $OrderColumnIdArray[] = $OrderColumnId ;
                $OrderColumnIdString .= '"'.$OrderColumnId.'",';
            }
        }


        $sql = '

                    DELETE FROM `order_column`
                         WHERE
                              `query_id` = :query_id

                    ';

        $OrderColumnIdString = trim($OrderColumnIdString, ",");
        if($OrderColumnIdString != "")
        {
            $sql .= '
                          AND `order_column_id` NOT IN ('.$OrderColumnIdString.');
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':query_id'=>$QueryId ));
        $return = $conn->execute();


        return $OrderColumnIdArray ;

    }

    public static function createUpdateOrderColumn($data, $QueryId, $OrderColumnId = null)
    {
        if(!empty($data))
        {
            self::printR($data , 0 );

            if($OrderColumnId != "" && $OrderColumnId!= null)
            {
                $OrderColumn = OrderColumn::findOne($OrderColumnId);
            }
            else
            {
                $OrderColumn = new OrderColumn();
            }


            $OrderColumn->setAttributes($data);
            $OrderColumn->query_id = $QueryId ;
            //self::printR($OrderColumn->attributes , 0);

            if(!$OrderColumn->save())
            {
                $html = AppBasic::getErrorMessageStraight($OrderColumn->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($OrderColumn->order_column_id);
                return $OrderColumn->order_column_id;
            }
        }
    }

    public static function handleCriteriaLinks($CriteriaId , $data)
    {
        $CriteriaLinkIdArray = array();
        $CriteriaLinks = self::arrayKeyExist('CriteriaLinks',$data);
        $CriteriaLink = self::arrayKeyExist('CriteriaLink',$CriteriaLinks);
        $CriteriaLinkIdString = "" ;

        if(!AppBasic::isSquential($CriteriaLink))
        {
            $CriteriaLinkN = array();
            $CriteriaLinkN[] = $CriteriaLink;
            $CriteriaLink = $CriteriaLinkN ;
        }

        for($i = 0 ;  $i <  sizeof($CriteriaLink); $i++)
        {
            $CriteriaLinkIdPrev = self::arrayKeyExist('CriteriaLinkId' , $CriteriaLink[$i]);
            $CriteriaLinkId = self::createUpdateCriteriaLink($CriteriaLink[$i], $CriteriaId, $CriteriaLinkIdPrev );
            if(self::stringNotNull($CriteriaLinkId))
            {
                $CriteriaLinkIdArray[] = $CriteriaLinkId ;
                $CriteriaLinkIdString .= '"'.$CriteriaLinkId.'",';
            }
        }


        $sql = '

                    DELETE 
                     FROM `criteria_links`
                         WHERE
                              `criteria_id` = :criteria_id

                    ';

        $CriteriaLinkIdString = trim($CriteriaLinkIdString, ",");
        if($CriteriaLinkIdString != "")
        {
            $sql .= '

                          AND `criteria_link_id` NOT IN ('.$CriteriaLinkIdString.');

                    ';
        }


        $conn = \Yii::$app->db->createCommand($sql, array(':criteria_id'=>$CriteriaId));
        $return = $conn->execute();


        return $CriteriaLinkIdArray ;
    }

    public static function createUpdateCriteriaLink($data, $CriteriaId, $CriteriaLinkId = null)
    {
        if(self::arrayNotEmpty($data)) {

            if ($CriteriaLinkId != "" && $CriteriaLinkId != null) {
                $CriteriaLink = CriteriaLinks::findOne($CriteriaLinkId);
            } else {
                $CriteriaLink = new CriteriaLinks();
            }

            $CriteriaLink->setAttributes($data);
            $CriteriaLink->criteria_id = $CriteriaId;
            //self::printR($CriteriaLink->attributes , 0);

            if (!$CriteriaLink->save()) {
                $html = AppBasic::getErrorMessageStraight($CriteriaLink->getErrors());
                trigger_error($html);
            } else {
                //self::printR($CriteriaLink->criteria_link_id);
                return $CriteriaLink->criteria_link_id;
            }
        }
    }

    public static function handlePageHeaderOrFooters($coreReportId , $data, $pageHeader = 1)
    {
        $PageHeaderOrFooterIdArray = array();
        if($pageHeader == 1)
        {
            $PageHeaderOrFooters = self::arrayKeyExist('PageHeaders',$data);
            $PageHeaderOrFooter = self::arrayKeyExist('PageHeader',$PageHeaderOrFooters);
        }
        else
        {
            $PageHeaderOrFooters = self::arrayKeyExist('PageFooters',$data);
            $PageHeaderOrFooter = self::arrayKeyExist('PageFooter',$PageHeaderOrFooters);
        }
        $PageHeaderOrFooterIdString = "" ;

        if(!AppBasic::isSquential($PageHeaderOrFooter))
        {
            $PageHeaderOrFooterN = array();
            $PageHeaderOrFooterN[] = $PageHeaderOrFooter;
            $PageHeaderOrFooter = $PageHeaderOrFooterN ;
        }

        for($i = 0 ;  $i <  sizeof($PageHeaderOrFooter); $i++)
        {
            $PageHeaderOrFooterIdPrev = self::arrayKeyExist('PageHeaderOrFooterId' , $PageHeaderOrFooter[$i]);
            $PageHeaderOrFooterId = self::createUpdatePageHeaderOrFooter($PageHeaderOrFooter[$i], $coreReportId, $PageHeaderOrFooterIdPrev);
            if(self::stringNotNull($PageHeaderOrFooterId))
            {
                $PageHeaderOrFooterIdArray[] = $PageHeaderOrFooterId ;
                $PageHeaderOrFooterIdString .= '"'.$PageHeaderOrFooterId.'",';
            }
        }


        $sql = '
                    DELETE FROM `page_header_or_footer`
                         WHERE
                              `core_report_id` = :core_report_id ';

        if($pageHeader)
        {
            $sql .= '  AND FooterText IS NULL  ';
        }
        else
        {
            $sql .= '  AND HeaderText IS NULL   ';
        }

        $PageHeaderOrFooterIdString = trim($PageHeaderOrFooterIdString, ",");
        if($PageHeaderOrFooterIdString != "")
        {

            $sql .= '
                         AND `page_header_or_footer_id` NOT IN ('.$PageHeaderOrFooterIdString.');
                    ';

        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId ));
        $return = $conn->execute();


        return $PageHeaderOrFooterIdArray ;
    }

    public static function createUpdatePageHeaderOrFooter($data, $coreReportId, $PageHeaderOrFooterId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($PageHeaderOrFooterId != "" && $PageHeaderOrFooterId!= null)
            {
                $PageHeaderOrFooter = PageHeaderOrFooter::findOne($PageHeaderOrFooterId);
            }
            else
            {
                $PageHeaderOrFooter = new PageHeaderOrFooter();
            }


            $PageHeaderOrFooter->setAttributes($data);
            $Format = self::arrayKeyExist('Format',$data);
            $PageHeaderOrFooter->setAttributes($Format);
            $PageHeaderOrFooter->core_report_id = $coreReportId ;
            //self::printR($PageHeaderOrFooter->attributes , 0);

            if(!$PageHeaderOrFooter->save())
            {
                $html = AppBasic::getErrorMessageStraight($PageHeaderOrFooter->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($PageHeaderOrFooter->page_header_or_footer_id);
                return $PageHeaderOrFooter->page_header_or_footer_id;
            }

        }
    }

    public static function handleDisplayOrders($coreReportId , $data)
    {
        $DisplayOrderIdArray = array();
        $DisplayOrders = self::arrayKeyExist('DisplayOrders',$data);
        $DisplayOrder = self::arrayKeyExist('DisplayOrder',$DisplayOrders);
        $DisplayOrderIdString = "" ;

        if(!AppBasic::isSquential($DisplayOrder))
        {
            $DisplayOrderN = array();
            $DisplayOrderN[] = $DisplayOrder;
            $DisplayOrder = $DisplayOrderN ;
        }

        for($i = 0 ;  $i <  sizeof($DisplayOrder); $i++)
        {
            $DisplayOrderIdPrev = self::arrayKeyExist('DisplayOrderId' , $DisplayOrder[$i]);
            $DisplayOrderId = self::createUpdateDisplayOrder($DisplayOrder[$i], $coreReportId, $DisplayOrderIdPrev);
            if(self::stringNotNull($DisplayOrderId))
            {
                $DisplayOrderIdArray[] = $DisplayOrderId ;
                $DisplayOrderIdString .= '"'.$DisplayOrderId.'",';
            }
        }

        $sql = '

                    DELETE FROM `display_order`
                         WHERE
                              `core_report_id` = :core_report_id

                    ';

        $DisplayOrderIdString = trim($DisplayOrderIdString, ",");
        if($DisplayOrderIdString != "")
        {
            $sql .= '
                         AND `display_order_id` NOT IN ('.$DisplayOrderIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId ));
        $return = $conn->execute();


        return $DisplayOrderIdArray ;
    }

    public static function createUpdateDisplayOrder($data, $coreReportId, $DisplayOrderId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($DisplayOrderId != "" && $DisplayOrderId!= null)
            {
                $DisplayOrder = DisplayOrder::findOne($DisplayOrderId);
            }
            else
            {
                $DisplayOrder = new DisplayOrder();
            }

            $DisplayOrder->setAttributes($data);
            $DisplayOrder->core_report_id = $coreReportId ;
            //self::printR($DisplayOrder->attributes , 0);

            if(!$DisplayOrder->save())
            {
                $html = AppBasic::getErrorMessageStraight($DisplayOrder->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($DisplayOrder->display_order_id);
                return $DisplayOrder->display_order_id;
            }
        }
    }

    public static function handleGroups($coreReportId , $data)
    {

        $GroupIdArray = array();
        $Groups = self::arrayKeyExist('Groups',$data);
        $Group = self::arrayKeyExist('Group',$Groups);
        $GroupIdString = "" ;

        if(!AppBasic::isSquential($Group))
        {
            $GroupN = array() ;
            $GroupN[] = $Group ;
            $Group = $GroupN ;
        }

        for($i = 0 ;  $i <  sizeof($Group); $i++)
        {
            $GroupIdPrev = self::arrayKeyExist('GroupId' , $Group[$i]);
            $GroupReturnArray = self::createUpdateGroup($Group[$i], $coreReportId, $GroupIdPrev);
            $GroupId = self::arrayKeyExist('GroupId' , $GroupReturnArray);
            if(self::stringNotNull($GroupId))
            {
                $GroupIdArray[] = $GroupReturnArray ;
                $GroupIdString .= '"'.$GroupId.'",';
            }
        }

        $GroupIdString = trim($GroupIdString, ",");




        $sql = '
                    DELETE `group_header` FROM `group_header`
                         INNER JOIN `group_tbl` ON `group_tbl`.`group_id` = `group_header`.`group_id`
                         WHERE
                              `group_tbl`.`core_report_id` = :core_report_id
                    ';

        if($GroupIdString != "")
        {
            $sql .= '
                         AND `group_tbl`.`group_id` NOT IN ('.$GroupIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId ));
        $return = $conn->execute();


        $sql = '
                    DELETE `group_trailer` FROM `group_trailer`
                         INNER JOIN `group_tbl` ON `group_tbl`.`group_id` = `group_trailer`.`group_id`
                         WHERE
                              `group_tbl`.`core_report_id` = :core_report_id
                    ';

        if($GroupIdString != "")
        {
            $sql .= '
                         AND `group_tbl`.`group_id` NOT IN ('.$GroupIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId ));
        $return = $conn->execute();


        $sql = '
                    DELETE FROM `group_tbl`
                         WHERE
                              `core_report_id` = :core_report_id
                    ';

        if($GroupIdString != "")
        {
            $sql .= '
                         AND `group_id` NOT IN ('.$GroupIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId ));
        $return = $conn->execute();



        return $GroupIdArray;

    }

    public static function createUpdateGroup($data, $coreReportId, $GroupId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($GroupId != "" && $GroupId!= null)
            {
                $Group = Group::findOne($GroupId);
            }
            else
            {
                $Group = new Group();
            }

            $Group->setAttributes($data);
            $Group->core_report_id = $coreReportId ;
            //self::printR($Group->attributes , 0);

            if(!$Group->save())
            {
                $html = AppBasic::getErrorMessageStraight($Group->getErrors());
                trigger_error($html);
            }
            else
            {
                $GroupId = $Group->group_id;
                $groupHeaderArray = self::handleGroupHeaders($GroupId , $data);
                $groupTrailerArray = self::handleGroupTrailers($GroupId , $data);
                //self::printR($GroupId);

                $groupArrayReturn  = array();
                $groupArrayReturn['GroupId'] = $GroupId ;
                $groupArrayReturn['headers']['GroupHeaderId'] = $groupHeaderArray ;
                $groupArrayReturn['trailers']['GroupTrailerId'] = $groupTrailerArray ;

                return $groupArrayReturn;
            }
        }
    }

    public static function handleGroupHeaders($GroupId , $data)
    {
        $GroupHeaderIdArray = array();
        $GroupHeaders = self::arrayKeyExist('GroupHeaders',$data);
        $GroupHeader = self::arrayKeyExist('GroupHeader',$GroupHeaders);
        $GroupHeaderIdString = "";

        if(!AppBasic::isSquential($GroupHeader))
        {
            $GroupHeaderN = array();
            $GroupHeaderN[] = $GroupHeader;
            $GroupHeader = $GroupHeaderN;
        }

        for($i=0;  $i<sizeof($GroupHeader);  $i++)
        {
            $GroupHeaderIdPrev = self::arrayKeyExist('GroupHeaderId' , $GroupHeader[$i]);
            $GroupHeaderId = self::createUpdateGroupHeader($GroupHeader[$i], $GroupId, $GroupHeaderIdPrev);
            if(self::stringNotNull($GroupHeaderId))
            {
                $GroupHeaderIdArray[] = $GroupHeaderId ;
                $GroupHeaderIdString .= '"'.$GroupHeaderId.'",';
            }
        }


        $sql = '
                    DELETE FROM `group_header`
                         WHERE
                              `group_id`=:group_id

                    ';

        $GroupHeaderIdString = trim($GroupHeaderIdString,",");
        if($GroupHeaderIdString != "")
        {
            $sql .= '
                         AND  `group_header_id` NOT IN ('.$GroupHeaderIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':group_id'=>$GroupId));
        $return = $conn->execute();


        return $GroupHeaderIdArray ;
    }

    public static function createUpdateGroupHeader($data, $GroupId, $GroupHeaderId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($GroupHeaderId != "" && $GroupHeaderId!= null)
            {
                $GroupHeader = GroupHeader::findOne($GroupHeaderId);
            }
            else
            {
                $GroupHeader = new GroupHeader();
            }

            $GroupHeader->setAttributes($data) ;
            $GroupHeader->group_id = $GroupId ;
            //self::printR($GroupHeader->attributes , 0);

            if(!$GroupHeader->save())
            {
                $html = AppBasic::getErrorMessageStraight($GroupHeader->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($GroupHeader->group_header_id , 0);
                return $GroupHeader->group_header_id;
            }
        }
    }

    public static function handleGroupTrailers($GroupId , $data)
    {
        $GroupTrailerIdArray = array();
        $GroupTrailers = self::arrayKeyExist('GroupTrailers',$data);
        $GroupTrailer = self::arrayKeyExist('GroupTrailer',$GroupTrailers);
        $GroupTrailerIdString = "" ;

        if(!AppBasic::isSquential($GroupTrailer))
        {
            $GroupTrailerN = array();
            $GroupTrailerN[] = $GroupTrailer;
            $GroupTrailer = $GroupTrailerN ;
        }

        for($i = 0 ;  $i <  sizeof($GroupTrailer); $i++)
        {
            $GroupTrailerIdPrev = self::arrayKeyExist('GroupTrailerId' , $GroupTrailer[$i]);
            $GroupTrailerId = self::createUpdateGroupTrailer($GroupTrailer[$i], $GroupId, $GroupTrailerIdPrev);
            if(self::stringNotNull($GroupTrailerId))
            {
                $GroupTrailerIdArray[] = $GroupTrailerId ;
                $GroupTrailerIdString .= '"'.$GroupTrailerId.'",';
            }
        }


        $sql = '

                    DELETE FROM `group_trailer`
                         WHERE
                              `group_id` = :group_id

                    ';

        $GroupTrailerIdString = trim($GroupTrailerIdString, ",");
        if($GroupTrailerIdString != "")
        {
            $sql .= '
                         AND `group_trailer_id` NOT IN ('.$GroupTrailerIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':group_id'=>$GroupId));
        $return = $conn->execute();

        return $GroupTrailerIdArray ;
    }

    public static function createUpdateGroupTrailer($data, $GroupId, $GroupTrailerId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($GroupTrailerId != "" && $GroupTrailerId!= null)
            {
                $GroupTrailer = GroupTrailer::findOne($GroupTrailerId);
            }
            else
            {
                $GroupTrailer = new GroupTrailer();
            }

            $GroupTrailer->setAttributes($data) ;
            $GroupTrailer->group_id = $GroupId ;
            //self::printR($GroupTrailer->attributes , 0);

            if(!$GroupTrailer->save())
            {
                $html = AppBasic::getErrorMessageStraight($GroupTrailer->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($GroupTrailer->group_trailer_id , 0);
                return $GroupTrailer->group_trailer_id;
            }
        }
    }

    public static function handleGraphs($coreReportId , $data)
    {
        $GraphIdArray = array();
        $Graphs = self::arrayKeyExist('Graphs',$data);
        $Graph = self::arrayKeyExist('Graph',$Graphs);
        $GraphIdString = "" ;

        if(!AppBasic::isSquential($Graph))
        {
            $GraphN = array();
            $GraphN[] = $Graph;
            $Graph = $GraphN;
        }

        for($i = 0 ;  $i <  sizeof($Graph); $i++)
        {
            $GraphIdPrev = self::arrayKeyExist('GraphId' , $Graph[$i]);
            $GraphIdArrayReturn = self::createUpdateGraph($Graph[$i], $coreReportId, $GraphIdPrev);
            $GraphId = self::arrayKeyExist('GraphId', $GraphIdArrayReturn);
            if(self::stringNotNull($GraphId))
            {
                $GraphIdArray[] = $GraphIdArrayReturn ;
                $GraphIdString .= '"'.$GraphId.'",';
            }
        }



        $sql = '

                    DELETE `plot` FROM `plot`
                         INNER JOIN `graph` ON `graph`.`graph_id` = `plot`.`graph_id`
                         WHERE `graph`.`core_report_id` = :core_report_id

                ';

        $GraphIdString = trim($GraphIdString, ",");
        if($GraphIdString != "")
        {
            $sql .= '
                           AND `graph`.`graph_id` NOT IN ('.$GraphIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId));
        $return = $conn->execute();



        $sql = '
                    DELETE FROM `graph`
                         WHERE
                              `core_report_id` = :core_report_id
                    ';

        $GraphIdString = trim($GraphIdString, ",");
        if($GraphIdString != "")
        {
            $sql = '
                         AND `graph_id` NOT IN ('.$GraphIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$coreReportId));
        $return = $conn->execute();


        return $GraphIdArray ;
    }

    public static function createUpdateGraph($data, $coreReportId, $GraphId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($GraphId != "" && $GraphId!= null)
            {
                $Graph = Graph::findOne($GraphId);
            }
            else
            {
                $Graph = new Graph();
            }

            $Graph->setAttributes($data) ;
            $Graph->core_report_id = $coreReportId ;
            //self::printR($Graph->attributes , 0);

            if(!$Graph->save())
            {
                $html = AppBasic::getErrorMessageStraight($Graph->getErrors());
                trigger_error($html);
            }
            else
            {
                $GraphId = $Graph->graph_id;
                $plotArray = self::handlePlots($GraphId , $data);

                $graphReturnArray =  array();
                $graphReturnArray['GraphId'] = $GraphId;
                $graphReturnArray['plot']['PlotId'] = $plotArray;


                //self::printR($Graph->group_trailer_id , 0);
                return $graphReturnArray;
            }
        }
    }

    public static function handlePlots($GraphId , $data)
    {
        $PlotIdArray = array();
        $Plots = self::arrayKeyExist('Plots',$data);
        $Plot = self::arrayKeyExist('Plot',$Plots);
        $PlotIdString = "" ;

        if(!AppBasic::isSquential($Plot))
        {
            $PlotN = array();
            $PlotN[] = $Plot;
            $Plot = $PlotN;
        }

        for($i = 0 ;  $i <  sizeof($Plot); $i++)
        {
            $PlotIdPrev = self::arrayKeyExist('PlotId' , $Plot[$i]);
            $PlotId = self::createUpdatePlot($Plot[$i], $GraphId, $PlotIdPrev);
            if(self::stringNotNull($PlotId))
            {
                $PlotIdArray[] = $PlotId ;
                $PlotIdString .= '"'.$PlotId.'",';
            }
        }


        $sql = '

                    DELETE FROM `plot`
                         WHERE
                              `graph_id` = :graph_id

                    ';

        $PlotIdString = trim($PlotIdString, ",");
        if($PlotIdString != "")
        {
            $sql .= '
                         AND `plot_id` NOT IN ('.$PlotIdString.')
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':graph_id'=>$GraphId));
        $return = $conn->execute();


        return $PlotIdArray ;
    }

    public static function createUpdatePlot($data, $GraphId, $PlotId = null)
    {
        if(self::arrayNotEmpty($data))
        {
            if($PlotId != "" && $PlotId!= null)
            {
                $Plot = Plot::findOne($PlotId);
            }
            else
            {
                $Plot = new Plot();
            }

            $Plot->setAttributes($data) ;
            $Plot->graph_id = $GraphId ;
            //self::printR($Plot->attributes , 0);

            if(!$Plot->save())
            {
                $html = AppBasic::getErrorMessageStraight($Plot->getErrors());
                trigger_error($html);
            }
            else
            {
                //self::printR($Plot->group_trailer_id , 0);
                return $Plot->plot_id;
            }
        }
    }

    public static function handleOutputTab($data , $coreReportId)
    {
        $ReportQuery = self::arrayKeyExist('ReportQuery',$data);
        $formatArray = self::arrayKeyExist('Format',$ReportQuery);
        $EntryForm = self::arrayKeyExist('EntryForm',$ReportQuery);
        $outputArray = self::arrayKeyExist('Output',$EntryForm);

        $pageHeadersArray = AppBasic::handlePageHeaderOrFooters($coreReportId, $outputArray);
        $pageFootersArray = AppBasic::handlePageHeaderOrFooters($coreReportId, $outputArray, 0);
        $displayOrderArray = AppBasic::handleDisplayOrders($coreReportId, $outputArray);
        $groupIdArray = AppBasic::handleGroups($coreReportId, $outputArray);
        $graphIdArray = AppBasic::handleGraphs($coreReportId, $outputArray);

        $outputTabReturnArray = array();
        $outputTabReturnArray['page_headers']['PageHeaderOrFooterId'] = $pageHeadersArray ;
        $outputTabReturnArray['page_footers']['PageHeaderOrFooterId'] = $pageFootersArray ;
        $outputTabReturnArray['display_order_set']['DisplayOrderId'] = $displayOrderArray ;
        $outputTabReturnArray['groups'] = $groupIdArray ;
        $outputTabReturnArray['graphs'] = $graphIdArray ;

        //self::printR($pageHeadersArray,0);
        //self::printR($outputTabReturnArray);
        return $outputTabReturnArray;
    }

    public static function createUpdateAssignments($data, $core_report_id = null, $QueryId = null)
    {
        if($core_report_id != null && $core_report_id != "")
        {
            $ReportQuery = self::arrayKeyExist('ReportQuery',$data) ;
            $formatArray = self::arrayKeyExist('Format',$ReportQuery) ;
            $EntryForm = self::arrayKeyExist('EntryForm',$ReportQuery) ;
            $AssignmentsArray = self::arrayKeyExist('Assignments',$EntryForm) ;
            $AssignmentArray = self::arrayKeyExist('Assignment',$AssignmentsArray) ;
        }
        else if($QueryId != null && $QueryId != "")
        {
            $EntryForm = $data ;
            $AssignmentsArray = self::arrayKeyExist('Assignments',$EntryForm) ;
            $AssignmentArray = self::arrayKeyExist('Assignment',$AssignmentsArray) ;

            //self::printR($data , 0);
            //self::printR($AssignmentArray , 0);
        }

        if(!AppBasic::isSquential($AssignmentArray))
        {
            $AssignmentArrayN = array();
            $AssignmentArrayN[] = $AssignmentArray;
            $AssignmentArray = $AssignmentArrayN ;
        }

        $AssignmentIdString = "" ;

        $AssignmentIdArray = array();
        if(is_array($AssignmentArray))
        {
            for($i = 0 ; $i < sizeof($AssignmentArray); $i++)
            {
                $assignment_id= self::arrayKeyExist('AssignmentId',$AssignmentArray[$i]);
                if($assignment_id!= "" && $assignment_id!= null)
                {
                    $Assignment =  Assignments::findOne($assignment_id);
                }
                else
                {
                    $Assignment = new Assignments();
                }


                $Assignment->setAttributes($AssignmentArray[$i]);
                $Assignment->core_report_id = $core_report_id ;
                $Assignment->query_id = $QueryId ;

                if(self::stringNotNUll( $Assignment->AssignName ) )
                {
                    //self::printR($Assignment->attributes);
                    if(!$Assignment->save())
                    {
                        $html = AppBasic::getErrorMessageStraight($Assignment->getErrors());
                        trigger_error($html);
                    }
                    else
                    {
                        $AssignmentId = $Assignment->assignments_id;
                        $AssignmentIdString .= '"'.$AssignmentId.'",';

                        $AssignmentIdArray[] = $AssignmentId;
                        //return $AssignmentId;
                    }
                }


            }
        }



        $sql = '
                    DELETE FROM `assignments`
                         WHERE ' ;

        if($core_report_id != "" && $core_report_id != null)
        {
            $idToCompare = $core_report_id ;
            $sql .= '     `core_report_id` = :idToCompare  ' ;
        }
        else if($QueryId != "" && $QueryId != null)
        {
            $idToCompare = $QueryId ;
            $sql .= '     `query_id` = :idToCompare  ' ;
        }

        $AssignmentIdString = trim($AssignmentIdString, ",");
        if($AssignmentIdString != "")
        {
            $sql .= '     AND `assignments_id` NOT IN ('.$AssignmentIdString.'); ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':idToCompare'=>$idToCompare));
        $return = $conn->execute();


        //self::printR($AssignmentIdArray);
        return $AssignmentIdArray;
    }

    public static function createUpdateCriterias($data, $core_report_id = null)
    {
        $ReportQuery = self::arrayKeyExist('ReportQuery',$data) ;
        $formatArray = self::arrayKeyExist('Format',$ReportQuery) ;
        $EntryForm = self::arrayKeyExist('EntryForm',$ReportQuery) ;
        $CriteriaArray = self::arrayKeyExist('Criteria',$EntryForm) ;
        $CriteriaItemArray = self::arrayKeyExist('CriteriaItem',$CriteriaArray) ;

        $CriteriaIdString = "" ;
        //self::printR($CriteriaArray);


        $CriteriaItemArray = self::makeItSquential($CriteriaItemArray);

        $CriteriaIdArray = array();
        if(is_array($CriteriaItemArray))
        {
            for($i = 0 ; $i < sizeof($CriteriaItemArray); $i++)
            {
                $Criteria_id = self::arrayKeyExist('CriteriaId',$CriteriaItemArray[$i]);
                if($Criteria_id  != "" && $Criteria_id != null)
                {
                    $Criteria =  Criteria::findOne($Criteria_id);
                }
                else
                {
                    $Criteria = new Criteria();
                }

                if($Criteria == null)
                {
                    $Criteria = new Criteria();
                }


                $Criteria->setAttributes($CriteriaItemArray[$i]);
                $Criteria->core_report_id = $core_report_id ;

                //self::printR($Criteria->attributes);
                if(!$Criteria->save())
                {
                    $html = AppBasic::getErrorMessageStraight($Criteria->getErrors());
                    trigger_error($html);
                }
                else
                {
                    $CriteriaId = $Criteria->criteria_id;
                    $CriteriaIdString .= '"'.$CriteriaId.'",';

                    $QueryReturnArray =  self::createUpdateQueries($CriteriaItemArray[$i] , null, $CriteriaId);
                    $CriteriaLinksArray =  self::handleCriteriaLinks($CriteriaId, $CriteriaItemArray[$i]);


                    $p = self::arrayKeyExist('Name',$CriteriaItemArray[$i]);

                    if($p != "")
                    {
                        $CriteriaIdArray[$p]['CriteriaId'] = $CriteriaId;
                        $QueryId = self::arrayKeyExist('QueryId',$QueryReturnArray);
                        $QueryColumns = self::arrayKeyExist('QueryColumns',$QueryReturnArray) ;
                        $assignment = self::arrayKeyExist('assignment',$QueryReturnArray) ;
                        if($QueryId != null && $QueryId != "")
                        {
                            $CriteriaIdArray[$p]['lookup_query']['QueryId'] = $QueryId;
                        }
                        if(!empty($QueryColumns))
                        {
                            $CriteriaIdArray[$p]['lookup_query']['columns']['QueryColumnId'] = $QueryColumns ;
                        }
                        if(!empty($assignment))
                        {
                            $CriteriaIdArray[$p]['lookup_query']['assignment']['assignment_id'] = $assignment ;
                        }


                        if(!empty($CriteriaLinksArray))
                        {
                            $CriteriaIdArray[$p]['lookup_query']['criteria_links']['CriteriaLinkId'] = $CriteriaLinksArray ;
                        }


                    }

                    //return $CriteriaId;
                }
            }
        }


        // delete assignments of criteriaItem which is to be deleted
        $sql = '
                        DELETE `criteria_links`
                          FROM `criteria_links`
                               INNER JOIN `criteria`
                                  ON `criteria`.`criteria_id` = `criteria_links`.`criteria_id`
                         WHERE     `criteria`.`core_report_id` = :core_report_id
                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();


        // delete assignments of criteriaItem which is to be deleted
        $sql = '
                        DELETE `assignments`
                          FROM `assignments`
                               INNER JOIN `query` ON `query`.`query_id` = `assignments`.`query_id`
                               INNER JOIN `criteria`
                                  ON `criteria`.`criteria_id` = `query`.`criteria_id`
                         WHERE     `criteria`.`core_report_id` = :core_report_id
                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';
        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();


        // delete query_column of criteriaItem which is to be deleted
        $sql = '
                        DELETE `order_column`
                          FROM `order_column`
                               INNER JOIN `query` ON `query`.`query_id` = `order_column`.`query_id`
                               INNER JOIN `criteria`
                                  ON `criteria`.`criteria_id` = `query`.`criteria_id`
                         WHERE     `criteria`.`core_report_id` = :core_report_id
                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';
        }


        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();


        // delete query_column of criteriaItem which is to be deleted
        $sql = '
                        DELETE `query_column`
                          FROM `query_column`
                               INNER JOIN `query` ON `query`.`query_id` = `query_column`.`query_id`
                               INNER JOIN `criteria`
                                  ON `criteria`.`criteria_id` = `query`.`criteria_id`
                         WHERE     `criteria`.`core_report_id` = :core_report_id
                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';
        }


        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();


        // delete query of criteriaItem which is to be deleted
        $sql = '

                        DELETE `query`
                          FROM `query`
                               INNER JOIN `criteria`
                                  ON `criteria`.`criteria_id` = `query`.`criteria_id`
                         WHERE     `criteria`.`core_report_id` = :core_report_id

                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';

        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();


        // delete criteriaItem
        $sql = '
                    DELETE FROM `criteria`
                         WHERE
                              `core_report_id` =:core_report_id
                    ';

        $CriteriaIdString = trim($CriteriaIdString, ",");
        if($CriteriaIdString != "")
        {
            $sql .= '
                               AND `criteria`.`criteria_id` NOT IN ('.$CriteriaIdString.');
                    ';

        }

        $conn = \Yii::$app->db->createCommand($sql, array(':core_report_id'=>$core_report_id));
        $return = $conn->execute();

        return $CriteriaIdArray;
    }


    public static function deleteHierarchy()
    {
        return [
            ['tables'=>['assignments','pre_sql','order_column','query_column'],
                'joinArray'=>[
                    ['joinTable'=>'query','joinTableField'=>'query_id','mainTableField'=>'query_id'],
                ],
                'whereField'=>'core_report_id','whereValue'=>null, 'function'=>'handleMultipleDelete'],
            ['tables'=>['assignments','pre_sql','order_column','query_column'],
                'joinArray'=>[
                    ['joinTable'=>'query','joinTableField'=>'query_id','mainTableField'=>'query_id'],
                    ['joinTable'=>'criteria','joinTableField'=>'criteria_id','mainTableField'=>'criteria_id'],
                ],
                'whereField'=>'core_report_id','whereValue'=>null, 'function'=>'handleMultipleDelete'],
            ['tables'=>['query','criteria_links'],
                'joinArray'=>[
                    ['joinTable'=>'criteria','joinTableField'=>'criteria_id','mainTableField'=>'criteria_id'],
                ],
                'whereField'=>'core_report_id','whereValue'=>null, 'function'=>'handleMultipleDelete'],
            ['tables'=>['group_header','group_trailer'],
                'joinArray'=>[
                    ['joinTable'=>'group_tbl','joinTableField'=>'group_id','mainTableField'=>'group_id'],
                ],
                'whereField'=>'core_report_id','whereValue'=>null, 'function'=>'handleMultipleDelete'],
            ['tables'=>['page_header_or_footer','data_source','display_order','assignments', 'query', 'criteria', 'group_tbl' , 'core_reports' ],'fieldName'=>'core_report_id','fieldValue'=>null, 'function'=>'handleSingleDelete'],
        ];
    }

    public static function deleteCoreReport($coreReportId)
    {
        $deleteHierarchy = self::deleteHierarchy();

        for($i=0;  $i < sizeof($deleteHierarchy); $i++)
        {
            $hierarchy = $deleteHierarchy[$i] ;

            $tables = self::arrayKeyExist('tables', $hierarchy);
            $joinArray = self::arrayKeyExist('joinArray', $hierarchy);
            $whereField = self::arrayKeyExist('whereField', $hierarchy);
            $whereValue = self::arrayKeyExist('whereValue', $hierarchy);
            $function = self::arrayKeyExist('function', $hierarchy);
            $fieldName = self::arrayKeyExist('fieldName', $hierarchy);
            $fieldValue = self::arrayKeyExist('fieldValue', $hierarchy);


            if(!self::stringNotNull($fieldValue))
            {
                $fieldValue = $coreReportId ;
            }

            if(!self::stringNotNull($whereValue))
            {
                $whereValue = $coreReportId ;
            }


            if($function == "handleSingleDelete")
            {
                if(self::arrayNotEmpty($tables))
                {
                    for($j = 0 ; $j < sizeof($tables); $j++)
                    {
                        $table = $tables[$j];
                        if(self::stringNotNull($table) && self::stringNotNull($fieldName) && self::stringNotNull($fieldValue))
                        {
                            self::handleSingleDelete($table , $fieldName, $fieldValue);
                        }
                    }
                }
            }
            else if($function == "handleMultipleDelete")
            {
                if(self::arrayNotEmpty($tables))
                {
                    for($j = 0 ; $j < sizeof($tables); $j++)
                    {
                        $table = $tables[$j];
                        if(self::stringNotNull($table) && self::stringNotNull($whereField) && self::stringNotNull($whereValue)&& self::arrayNotEmpty($joinArray))
                        {
                            self::handleMultipleDelete($table ,$joinArray, $whereField, $whereValue);
                        }
                    }
                }
            }

        }
    }

    public static function handleSingleDelete($table, $fieldName, $fieldValue)
    {

        $sql = ' DELETE FROM `'.$table.'`
                         WHERE
                              `'.$fieldName.'` = "'.$fieldValue.'" ; ';

        $conn = \Yii::$app->db->createCommand($sql);
        $return = $conn->execute();

        //echo  $sql ;
        //self::printRT($return);
    }

    public static function handleMultipleDelete($mainTable, $joinArray, $whereField, $whereValue)
    {

        $sql = '  DELETE  `'.$mainTable.'` FROM `'.$mainTable.'` ';

        for($i = 0; $i < sizeof($joinArray); $i++)
        {
            $join = $joinArray[$i];
            $joinTable = $join['joinTable'];
            $joinTableField = $join['joinTableField'];
            $mainTableField = $join['mainTableField'];

            if($i == 0)
            {
                $mainTableForJoin = $mainTable ;
            }
            else
            {
                $mainTableForJoin = $joinArray[$i-1]['joinTable'];
            }

            $sql .= ' INNER JOIN `'.$joinTable.'` ON `'.$joinTable.'`.`'.$joinTableField.'` =  `'.$mainTableForJoin.'`.`'.$mainTableField.'` ';

        }

        $sql .= ' WHERE `'.$joinTable.'`.`'.$whereField.'` = "'.$whereValue.'"  ; ';


        $conn = \Yii::$app->db->createCommand($sql);
        $return = $conn->execute();

        //echo  $sql ;
        //self::printRT($return);
    }


    public static function listOfExceptions($id)
    {
        $array =  [
            '1001'=>'There is no data in POST request we get. 1001',
            '1002'=>'There is no data in POST request we get. 1002 ',
            '1003'=>'There is no data in POST request we get. 1003',
            '1004'=>'There is no data in POST request we get. 1004'
        ];

        return self::arrayKeyExist($id, $array);
    }

    public static function handle_inserting_to_xml( $at, $k, $v)
    {
        if(is_array($v))
        {
            $ds = $at->add_xmlval ( $k );
            foreach($v as $key=>$val)
            {
                if(!is_numeric($key))
                {
                    $el = self::handle_inserting_to_xml($ds, $key, $val);
                }
                else
                {
                    $new_k = substr($k , 0, (strlen($k)-1) ) ;
                    $new_k = $new_k."_mos";
                    $el = self::handle_inserting_to_xml($ds, $new_k, $val);
                }
            }

            return $ds ;
        }
        else
        {
            $el =&  $at->add_xmlval ( $k, $v );
        }

        return $el;
    }

    public static function handleDbIdsIntoXML($queryXmlObject, $ids, $obj = 1, $text = null)
    {
        if(is_array($ids))
        {
            foreach($ids as $k=>$val)
            {


//                if($k == "GraphId") {
//
//                    self::printR("ENTRY START", 0);
//                    self::printR("k " . $k, 0);
//                    self::printR("val ", 0);
//                    self::printR($val, 0);
//                    self::printR("queryXmlObject   ", 0);
//                    self::giveDetailP($queryXmlObject);
//
//                    if($k == "PageHeaderOrFooterId")
//                    {
//                        self::printR($queryXmlObject , 0);
//                    }
//
//                    if(self::giveDetail($queryXmlObject) == "OBJECT" || self::giveDetail($queryXmlObject) == "STRING"){
//                        if (self::propertyExist($k, $queryXmlObject)){
//                            self::printR("There is  property  ", 0);
//                        } else {
//                            self::printR("There is no property with ", 0);
//                        }
//                    }
//
//                    self::printR("ENTRY ENDS");
//
//                }



                if(is_array($val))
                {
                    if(AppBasic::isSquential($val))
                    {


                        if($k == "lookup_queries")
                        {
//                            self::printR( "START FFF" , 0);
//                            self::printR( $k , 0);
//                            self::printR( $val , 0);
//                            self::giveDetail($queryXmlObject);
//                            self::printR( "OUT FFF");
                        }

                        if(is_object($queryXmlObject))
                        {
                            if($k == "lookup_queries")
                            {
                                 $count = 0 ;
                                 foreach($queryXmlObject->$k as $p=>$pVal)
                                 {
                                     $entryVal = self::arrayKeyExist($p , $val) ;
                                     if(!empty($entryVal))
                                     {

                                         if(is_array( $queryXmlObject->$k[$p] ))
                                         {

                                             if(property_exists($queryXmlObject->$k[$p] , 'CriteriaId'))
                                             {
                                                 self::printR("There is  property  " , 0);
                                                 // self::printR($queryXmlObject->$k , 0);
                                             }
                                             else
                                             {
                                                 self::printR("There is no property with " , 0);
                                             }



                                             $queryXmlObject->$k[$p] =  self::handleDbIdsIntoXML( $queryXmlObject->$k[$p] , $entryVal );
                                         }
                                         else
                                         {
                                             self::printR("THIS IS NOT AN ARRAY");
                                         }

                                     }
                                 }
                            }
                            else if($k == "graphs")
                            {

                                if(self::propertyExist('graphs',$queryXmlObject))
                                {
                                    //self::printR("COMING HERE FINALLY");
                                    for($i = 0 ; $i < sizeof($val); $i++)
                                    {
                                        if(self::propertyExist('GraphId',$queryXmlObject->graphs[$i], 0) && self::arrayKeyExist('GraphId',$val[$i],0))
                                        {
                                            $queryXmlObject->graphs[$i]->GraphId = $val[$i]['GraphId'];

                                            if(self::propertyExist('plot',$queryXmlObject->graphs[$i], 0) && self::arrayKeyExist('plot',$val[$i], 0))
                                            {
                                                $plot = self::arrayKeyExist('PlotId',$val[$i]['plot']);
                                                for($z=0; $z<sizeof($plot); $z++)
                                                {
                                                    if(self::arrayKeyExist($z,$queryXmlObject->graphs[$i]->plot, 0))
                                                    {
                                                        $queryXmlObject->graphs[$i]->plot[$z]['PlotId'] = $plot[$z];
                                                    }
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                            else if($k == "groups")
                            {
                                if(self::propertyExist('groups',$queryXmlObject))
                                {
                                    //self::printR("COMING HERE FINALLY");
                                    for($i = 0 ; $i < sizeof($val); $i++)
                                    {
                                        if(self::propertyExist('GroupId',$queryXmlObject->groups[$i], 0) && self::arrayKeyExist('GroupId',$val[$i],0))
                                        {
                                            $queryXmlObject->groups[$i]->GroupId = $val[$i]['GroupId'];

                                            if(self::propertyExist('headers',$queryXmlObject->groups[$i], 0) && self::arrayKeyExist('headers',$val[$i], 0))
                                            {
                                                $headers = self::arrayKeyExist('GroupHeaderId',$val[$i]['headers']);
                                                for($z=0; $z<sizeof($headers); $z++)
                                                {
                                                    if(self::arrayKeyExist($z,$queryXmlObject->groups[$i]->headers, 0))
                                                    {
                                                        $queryXmlObject->groups[$i]->headers[$z]['GroupHeaderId'] = $headers[$z];
                                                    }
                                                }
                                            }

                                            if(self::propertyExist('trailers',$queryXmlObject->groups[$i], 0) && self::arrayKeyExist('trailers',$val[$i], 0))
                                            {
                                                $trailers = self::arrayKeyExist('GroupTrailerId',$val[$i]['trailers']);
                                                for($z=0; $z<sizeof($trailers); $z++)
                                                {
                                                    if(self::arrayKeyExist($z,$queryXmlObject->groups[$i]->trailers, 0))
                                                    {
                                                        $queryXmlObject->groups[$i]->trailers[$z]['GroupTrailerId'] = $trailers[$z];
                                                    }
                                                }
                                            }

                                        }
                                    }

                                }
                            }

                        }
                        else if(AppBasic::isSquential($queryXmlObject))
                        {
                            if($k == "PreSqlId")
                            {
                                for($i = 0 ; $i < sizeof($val); $i++)
                                {
                                    $SQLText =  self::arrayKeyExist('SQLText', $queryXmlObject[$i] ) ;
                                    $queryXmlObject[$i] = array() ;
                                    $queryXmlObject[$i]['SQLText'] = $SQLText ;
                                    $queryXmlObject[$i][$k]  = $val[$i] ;
                                }
                            }
                            else
                            {
                                if($k == "CriteriaLinkId")
                                {
                                    for($i = 0 ; $i < sizeof($val); $i++)
                                    {
                                        $queryXmlObject[$i][$k]  = $val[$i] ;
                                    }
                                }
                                else
                                {
                                    for($i = 0 ; $i < sizeof($val); $i++)
                                    {
                                        if(self::propertyExist($k , $queryXmlObject[$i], 0))
                                        {
                                            $queryXmlObject[$i]->$k  = $val[$i] ;
                                        }
                                    }

                                    //self::printR("YOU ARE RIGHT COMING HERE", 0);
                                    //self::printR($queryXmlObject);
                                }
                            }

                            return $queryXmlObject ;
                        }
                        else
                        {
                            if($k == "lookup_query")
                            {
                                $queryXmlObjectDup = $queryXmlObject;
                                foreach($queryXmlObjectDup as $keyObject=>$queryXmlObjectDupF)
                                {
                                    for($x = 0 ;$x < sizeof($val); $x++)
                                    {
                                        //self::printR( "START FFF" , 0);
                                        //self::printR( $k , 0);
                                        //self::printR( $val[$x] , 0);
                                        //self::printR( "OUT FFF" , 0);

                                        $queryXmlObjectDup[$keyObject]->$k = self::handleDbIdsIntoXML( $queryXmlObjectDup[$keyObject]->$k , $val[$x], 0 , $text = "TTTTTTTTTTTTTTT" );

                                    }
                                }

                                $queryXmlObject = $queryXmlObjectDup ;

                            }
                            else if($k = "DisplayOrderId")
                            {
                                for($x = 0 ;$x < sizeof($val); $x++)
                                {
                                    $hasItemKey = self::arrayKeyExist('itemno',$queryXmlObject,0);
                                    if($hasItemKey)
                                    {
                                       $item = self::arrayKeyExist('itemno',$queryXmlObject);
                                       $hasItemKey = self::arrayKeyExist($x,$queryXmlObject['itemno'], 0);
                                       if($hasItemKey)
                                       {
                                           $queryXmlObject[$k][$x] = $val[$x];
                                       }
                                    }
                                }

                            }
                            else
                            {
                                if(self::propertyExist($k , $queryXmlObject))
                                {
                                    self::handleDbIdsIntoXML( $queryXmlObject->$k , $val, 0);
                                }
                            }
                        }
                    }
                    else
                    {
                        if(is_array($queryXmlObject))
                        {
                            if(AppBasic::isSquential($queryXmlObject))
                            {

/*
                                self::printR($queryXmlObject , 0);
                                self::printR($k , 0);
                                self::printR($val , 0);
*/

                                for($f = 0; $f < sizeof($queryXmlObject); $f++)
                                {
                                    if(self::arrayKeyExist($f , $queryXmlObject) && self::arrayKeyExist($f , $val))
                                    {
                                        if(self::propertyExist($k , $queryXmlObject[$f] ))
                                        {
                                            $queryXmlObject[$f]->$k = $val[$f] ;
                                        }
                                    }
                                }

                                //self::printR( "SEQUENTIAL" );
                            }
                            else
                            {
                                //self::printR( "START9" , 0);
                                //self::printR($queryXmlObject[$k] , 0);
                                //self::printR($val , 0);
                                //self::printR($k , 0);
                                $queryXmlObject[$k] = $val ;
                                //self::printR( $queryXmlObject , 0);
                                //self::printR( "END" , 0);

                                return $queryXmlObject;
                            }
                        }
                        else
                        {



                            if($k == "criteria_links" )
                            {
//                                self::printR( "COLUMNS SHOW" , 0);
//                                //self::printR(  $queryXmlObject  , 0);
//                                self::printR( !empty($val) , 0);
//                                self::printR( "COLUMNS ENDS" , 0);
                                //self::printR($k , 0);
                                //self::printR($val , 0);
                                //self::printR($queryXmlObject , 0);
                                //self::printR( "CHECK ENDS" , 0);
                            }


                            //self::printR( "START3" , 0);
                            //self::printR($queryXmlObject->$k[''] , 0);
                            //self::printR($val , 0);
                            //self::printR($k , 0);
                            //self::printR( "END" , 0);

                            if($k == "lookup_queries")
                            {



                                if(is_array($queryXmlObject->$k))
                                {
                                    foreach($queryXmlObject->$k as $p=>$pVal)
                                    {
                                        $entryVal = self::arrayKeyExist($p , $val) ;
                                        if(!empty($entryVal))
                                        {


                                            //self::printR( "Array" ,0);
                                            //self::printR( $p ,0);
                                            //self::printR($pVal , 0 );

                                            // $queryXmlObject->$k[$p] ))
                                            //{

                                                if(property_exists($pVal , 'CriteriaId'))
                                                {
                                                    //self::printR("There is  property  " , 0);
                                                    //self::printR($queryXmlObject->$k , 0);
                                                }
                                                else
                                                {
                                                    //self::printR("There is no property with " , 0);
                                                }

                                            $pVal = self::handleDbIdsIntoXML( $pVal , $entryVal );

                                            //}
                                            //else
                                            //{
                                              //  self::printR("THIS IS NOT AN ARRAY");
                                            //}

                                        }
                                    }

                                }
                                else
                                {
//                                    self::printR("lookup_queries is not an object");
                                }

                            }
                            else if($k == "lookup_query")
                            {
                                $queryXmlObject->$k =  self::handleDbIdsIntoXML( $queryXmlObject->$k , $val );
                            }
                            else if(!empty($queryXmlObject->$k) && !empty($val))
                            {

                                if($k == "assignment" || $k == "columns")
                                {
//                                    self::printR( "START3" , 0);
//                                    self::printR($text , 0);
//                                    self::printR($val , 0);
//                                    self::printR($k , 0 );
//                                    self::printR( "END" , 0);
                                }

                                //self::handleDbIdsIntoXML( $queryXmlObject->$k , $val);
                                $queryXmlObject->$k = self::handleDbIdsIntoXML( $queryXmlObject->$k , $val );
                            }


                        }

                    }

                }
                else
                {

                    if(is_array($queryXmlObject))
                    {
                        if(AppBasic::isSquential($queryXmlObject))
                        {
                            //self::printR( "START11" , 0);
                            //self::printR($queryXmlObject , 0);
                            //self::printR($val , 0);
                            //self::printR($k , 0);
                            //self::printR( "END" , 0);

//                            self::printR( "SEQUENTIAL222" );
                        }
                        else
                        {
                            //self::printR( "START12" , 0);
                            //self::printR($queryXmlObject , 0);
                            //self::printR($val , 0);
                            //self::printR($k , 0);
                            $queryXmlObject[$k] = $val ;
                            //self::printR( $queryXmlObject , 0);
                            //self::printR( "END" , 0);

                            return $queryXmlObject;
                        }
                    }
                    else
                    {


                        $queryXmlObject->$k = $val ;
                        if($k == "CriteriaId" ||  $k == "QueryId")
                        {
//                            self::printR( "START YYYY" , 0);
//                            self::printR($queryXmlObject->$k , 0);
//                            self::printR($val , 0);
//                            self::printR($k , 0);
//                            self::printR( "END" , 0);
                        }

                        //self::printR( "START5" , 0);
                        //self::printR($queryXmlObject[$k] , 0);
                        //self::printR($val , 0);
                        //self::printR($k , 0);
                        //self::printR( "END" , 0);




                    }
                }
            }
        }
        else
        {
            self::printR( "ids" );
            //self::printR( "ids" , 0);
            return $ids ;
        }

        return $queryXmlObject;
    }

    public static function manageCoreReport($coreReportId = null)
    {
        //echo "<br/>coreReportId:::<br/>";
        //echo "$coreReportId";

        $newDataArray = array();
        $newDataArray['ReportQuery'] = array();
        $z =& $newDataArray['ReportQuery'];

        $coreReportData = self::retrieveCoreReportFromDatabase($coreReportId);
        $queryData = self::retrieveQueryFromDatabase($coreReportId);
        $criteriaItemData = self::retrieveCriteriaFromDatabase($coreReportId);

        //self::printRT($coreReportData , 0);

        $z['Format'] = self::forLoopData($coreReportData, 'Format');
        $f =& $z['Format'];
        $z['Datasource'] = self::forLoopData($coreReportData,"Datasource");
        $d =& $z['Datasource'];
        $z['EntryForm'] = array();
        $e =& $z['EntryForm'];

        //self::printRT($z , 0);

        $Query = self::forLoopData($queryData, 'Query');
        $e['Query'] = $Query;

        $Assignments = self::forLoopData($coreReportData, 'Assignments');
        $e['Assignments'] = $Assignments;

        //self::printRT($criteriaItemData , 'criteriaItemData');
        $Criteria = self::forLoopData($criteriaItemData, 'Criteria');
        $e['Criteria'] = $Criteria;

        $Output = self::forLoopData($coreReportData, "Output");
        $e['Output'] = $Output;

        return $newDataArray;
    }


    public static function forLoopData($data, $what)
    {
        $newArray = array();
        $newArray1 = array();
        $newArray2 = array();
        $newArray3 = array();
        $newArray4 = array();
        $newArray5 = array();
        $newArray6 = array();
        $newArray7 = array();
        $newArray8 = array();
        $newArray9 = array();
        $newArray10 = array();


        $last_queryColumnId = array();
        $last_preSqlId = array();
        $last_orderColumnId = array();
        $last_assignmentId = array();
        $last_CriteriaId = array();
        $last_QueryId = array();
        $last_QueryColumnId = array();
        $last_OrderColumnId = array();
        $last_AssignmentId = array();
        $last_CriteriaLinkId = array();

        $last_PageHeaderId = array();
        $last_PageFooterId = array();
        $last_GroupId = array();
        $last_GroupHeaderId = array();
        $last_GroupTrailerId= array();
        $last_DisplayOrderId = array();
        $last_GraphId = array();
        $last_PlotId = array();

        $last_CriteriaQueryId = [];
        $last_CriteriaQueryColumnId = [];
        $last_CriteriaOrderColumnId = [];
        $last_CriteriaAssignmentId = [];


        for($i=0; $i<sizeof($data); $i++)
        {

            $queryColumnId = self::arrayKeyExist('query_column__QueryColumnId',$data[$i]);
            $p = self::arrayKeyExist('QueryColumns',$newArray) ?  sizeof( $newArray['QueryColumns'])  : 0 ;

            $preSqlId = self::arrayKeyExist('pre_sql__PreSqlId',$data[$i]);
            $q = self::arrayKeyExist('PreSQLS',$newArray) ?  sizeof( $newArray['PreSQLS'])  : 0 ;

            $orderColumnId = self::arrayKeyExist('order_column__OrderColumnId',$data[$i]);
            $r = self::arrayKeyExist('OrderColumns',$newArray) ?  sizeof( $newArray['OrderColumns'])  : 0 ;

            $assignmentId = self::arrayKeyExist('assignments__AssignmentId',$data[$i]);
            $s = sizeof( $newArray )  ;

            $CriteriaId = self::arrayKeyExist('criteria__CriteriaId',$data[$i]);
            $t = sizeof( $newArray )  ;

            $QueryId_UnderCriteria = self::arrayKeyExist('queryUnderCriteria__QueryId',$data[$i]);
            $u = sizeof( $newArray ) ;

            $QueryColumnId_UnderCriteria = self::arrayKeyExist('queryColumnUnderCriteria__QueryColumnId',$data[$i]);
            if(!in_array($CriteriaId, $last_CriteriaId))
            {
                if(!empty($last_CriteriaId) && $t!=0)
                {
                    $tException = $t==0 ? $t : $t-1 ;
                    $newArray[$tException]['Query']['QueryColumns'] = $newArray1;
                }
                $newArray1 = array();
            }
            $v = sizeof($newArray1);

            $OrderColumnId_UnderCriteria = self::arrayKeyExist('orderColumnUnderCriteria__OrderColumnId',$data[$i]);
            if(!in_array($CriteriaId, $last_CriteriaId))
            {
                if(!empty($last_CriteriaId) && $t!=0)
                {
                    $tException = $t ==0 ? $t : $t-1 ;
                    $newArray[$tException]['Query']['OrderColumns'] = $newArray2;
                }
                $newArray2 = array();
            }
            $w = sizeof($newArray2);

            $AssignmentId_UnderCriteria = self::arrayKeyExist('assignmentsUnderCriteria__AssignmentId',$data[$i]);
            if(!in_array($CriteriaId, $last_CriteriaId))
            {
                if(!empty($last_CriteriaId) && $t!=0)
                {
                    $tException = $t ==0 ? $t : $t-1 ;
                    $newArray[$tException]['Query']['Assignments'] = $newArray3;
                }
                $newArray3 = array();
            }
            $x = sizeof($newArray3);

            $CriteriaLinkId = self::arrayKeyExist('criteria_links__CriteriaLinkId',$data[$i]);
            if(!in_array($CriteriaId, $last_CriteriaId))
            {
                if(!empty($last_CriteriaId)  && $t!=0 )
                {
                    $tException = $t ==0 ? $t : $t-1 ;
                    $newArray[$tException]['CriteriaLinks'] = $newArray4;
                }
                $newArray4 = array();
            }
            $y = sizeof($newArray4);

            $PageHeaderId = self::arrayKeyExist('pageHeader__PageHeaderOrFooterId', $data[$i]);
            $z = sizeof($newArray1);

            $PageFooterId = self::arrayKeyExist('pageFooter__PageHeaderOrFooterId', $data[$i]);
            $j = sizeof($newArray2);

            $DisplayOrderId = self::arrayKeyExist('display_order__DisplayOrderId', $data[$i]);
            $f = sizeof($newArray3);

            $GroupId = self::arrayKeyExist('group_tbl__GroupId', $data[$i]);
            $l = sizeof($newArray4);

            $GroupHeaderId = self::arrayKeyExist('group_header__GroupHeaderId', $data[$i]);
            if(!in_array($GroupId, $last_GroupId))
            {
                if(!empty($last_GroupId) && $l!=0 )
                {
                    $lException = $l == 0 ? $l : $l-1 ;

                    $newArray4[$lException]['GroupHeaders'] = $newArray5;
                }
                $newArray5 = array();
            }
            $m = sizeof($newArray5);

            $GroupTrailerId = self::arrayKeyExist('group_trailer__GroupTrailerId', $data[$i]);
            if(!in_array($GroupId, $last_GroupId))
            {
                if(!empty($last_GroupId)  && $l!=0 )
                {
                    $lException = $l == 0 ? $l : $l-1 ;
                    $newArray4[$lException]['GroupTrailers'] = $newArray6;
                }
                $newArray6 = array();
            }
            $n = sizeof($newArray6);

            $GraphId = self::arrayKeyExist('graph__GraphId', $data[$i]);
            $o = sizeof($newArray7);

            $PlotId = self::arrayKeyExist('plot__PlotId', $data[$i]);
            if(!in_array($GraphId, $last_GraphId))
            {
                if(!empty($last_GraphId)  && $o!=0  )
                {
                    $oException = $o == 0 ? $o : $o-1 ;
                    $newArray7[$oException]['Plots'] = $newArray8;
                }
                $newArray8 = array();
            }
            $e = sizeof($newArray8);

            foreach($data[$i] as $k=>$val)
            {

                $key = explode("__",$k);
                $case = self::arrayKeyExist(0,$key);
                $key_of_array = self::arrayKeyExist(1,$key);

                if(self::stringNotNull($key_of_array) && self::stringNotNull($case))
                {
                    switch($case)
                    {

                        case "core_reports":

                            if(self::getArrayInnerKey($key_of_array,$case) && $what == "Format")
                            {
                                $newArray[$key_of_array] = $val;
                            }

                            break;

                        case "data_source":

                            if($what == "Datasource")
                            {
                                if(self::getArrayInnerKey($key_of_array,$case))
                                {
                                    $newArray['SourceConnection'][$key_of_array] = $val;
                                }
                                else
                                {
                                    $newArray[$key_of_array] = $val;
                                }
                            }

                            break;

                        case "query":

                            if($what == "Query")
                            {
                                if(self::getArrayInnerKey($key_of_array,$case))
                                {
                                    $newArray['SQL'][$key_of_array] = $val;
                                }
                                else
                                {
                                    $newArray[$key_of_array] = $val;
                                }
                            }

                            break;

                        case "query_column":

                            if($what == "Query" && ($i == 0 || (!in_array($queryColumnId, $last_queryColumnId))) && self::stringNotNull($queryColumnId))
                            {
                                $newArray['QueryColumns'][$p][$key_of_array] = $val;
                            }
                            else if(!self::arrayKeyExist("QueryColumns",$newArray))
                            {
                                $newArray['QueryColumns'] = array();
                            }

                            break;

                        case "pre_sql":

                            if($what == "Query" && ($i == 0 || (!in_array($preSqlId, $last_preSqlId))) && self::stringNotNull($preSqlId))
                            {
                                $newArray['PreSQLS'][$q][$key_of_array] = $val;
                            }
                            else if(!self::arrayKeyExist("PreSQLS",$newArray))
                            {
                                $newArray['PreSQLS'] = array();
                            }

                            break;

                        case "order_column":

                            if($what == "Query" && ($i == 0 || (!in_array($orderColumnId, $last_orderColumnId))) && self::stringNotNull($orderColumnId))
                            {
                                $newArray['OrderColumns'][$r][$key_of_array] = $val;
                            }
                            else if(!self::arrayKeyExist("OrderColumns",$newArray))
                            {
                                $newArray['OrderColumns'] = array();
                            }

                            break;

                        case "assignments":

                            if($what == "Assignments" && ($i == 0 || (!in_array($assignmentId, $last_assignmentId))) && self::stringNotNull($assignmentId))
                            {
                                $newArray[$s][$key_of_array] = $val;
                            }
                            else if(sizeof($newArray) == 0)
                            {
                                $newArray = array();
                            }

                            break;

                        case "criteria":

                            if($what == "Criteria" && ($i == 0 || (!in_array($CriteriaId, $last_CriteriaId))) && self::stringNotNull($CriteriaId))
                            {
                                $newArray[$t][$key_of_array] = $val;
                            }
                            else if(sizeof($newArray) == 0)
                            {
                                $newArray = array();
                            }

                            break;

                        case "queryUnderCriteria":

                            if($what == "Criteria" && ($i == 0 || (!in_array($QueryId_UnderCriteria, $last_CriteriaQueryId))) && self::stringNotNull($QueryId_UnderCriteria))
                            {
                                $t = in_array($CriteriaId, $last_CriteriaId) ? (sizeof($newArray)-1) : $t ;

                                if(self::getArrayInnerKey($key_of_array,$case))
                                {
                                    $newArray[$t]['Query']['SQL'][$key_of_array] = $val;
                                }
                                else
                                {
                                    $newArray[$t]['Query'][$key_of_array] = $val;
                                }
                            }

                            break;

                        case "queryColumnUnderCriteria":


                            if($what == "Criteria" && ($i == 0 || (!in_array($QueryColumnId_UnderCriteria, $last_CriteriaQueryColumnId))) && self::stringNotNull($QueryColumnId_UnderCriteria))
                            {
                                $newArray1[$v][$key_of_array] = $val;
                            }

                            break;

                        case "orderColumnUnderCriteria":

                            if($what == "Criteria" && ($i == 0 || (!in_array($OrderColumnId_UnderCriteria, $last_CriteriaOrderColumnId))) && self::stringNotNull($OrderColumnId_UnderCriteria))
                            {
                                $newArray2[$w][$key_of_array] = $val;
                            }

                            break;

                        case "assignmentsUnderCriteria":


                            if($what == "Criteria" && ($i == 0 || (!in_array($AssignmentId_UnderCriteria, $last_CriteriaAssignmentId))) && self::stringNotNull($AssignmentId_UnderCriteria))
                            {
                                $newArray3[$x][$key_of_array] = $val;
                            }

                            break;

                        case "criteria_links":

                            if($what == "Criteria" && ($i == 0 || (!in_array($CriteriaLinkId, $last_CriteriaLinkId))) && self::stringNotNull($CriteriaLinkId))
                            {
                                $newArray4[$y][$key_of_array] = $val;
                            }

                            break;

                        case "pageHeader":

                            if($what == "Output" && ($i == 0 || (!in_array($PageHeaderId, $last_PageHeaderId))) && self::stringNotNull($PageHeaderId))
                            {
                                if(self::getArrayInnerKey($key_of_array,$case))
                                {
                                    $newArray1[$z]['Format'][$key_of_array] = $val;
                                }
                                else
                                {
                                    $newArray1[$z][$key_of_array] = $val;
                                }
                            }

                            break;

                        case "pageFooter":

                            if($what == "Output" && ($i == 0 || (!in_array($PageFooterId, $last_PageFooterId))) && self::stringNotNull($PageFooterId))
                            {
                                if(self::getArrayInnerKey($key_of_array,"pageHeader"))
                                {
                                    $newArray2[$j]['Format'][$key_of_array] = $val;
                                }
                                else
                                {
                                    $newArray2[$j][$key_of_array] = $val;
                                }
                            }

                            break;

                        case "display_order":

                            if($what == "Output" && ($i == 0 || (!in_array($DisplayOrderId, $last_DisplayOrderId))) && self::stringNotNull($DisplayOrderId))
                            {
                                $newArray3[$f][$key_of_array] = $val;
                            }

                            break;

                        case "group_tbl":

                            if($what == "Output" && ($i == 0 || (!in_array($GroupId, $last_GroupId))) && self::stringNotNull($GroupId))
                            {
                                $newArray4[$l][$key_of_array] = $val;
                            }

                            break;

                        case "group_header":

                            if($what == "Output" && ($i == 0 || (!in_array($GroupHeaderId, $last_GroupHeaderId))) && self::stringNotNull($GroupHeaderId))
                            {
                                $newArray5[$m][$key_of_array] = $val;
                            }

                            break;

                        case "group_trailer":

                            if($what == "Output" && ($i == 0 || (!in_array($GroupTrailerId, $last_GroupTrailerId))) && self::stringNotNull($GroupTrailerId))
                            {
                                $newArray6[$n][$key_of_array] = $val;
                            }

                            break;

                        case "graph":

                            if($what == "Output" && ($i == 0 || (!in_array($GraphId, $last_GraphId))) && self::stringNotNull($GraphId))
                            {
                                $newArray7[$o][$key_of_array] = $val;
                            }

                            break;

                        case "plot":

                            if($what == "Output" && ($i == 0 || (!in_array($PlotId, $last_PlotId))) && self::stringNotNull($PlotId))
                            {
                                $newArray8[$e][$key_of_array] = $val;
                            }

                            break;

                        case "":
                            break;

                        default:
                            break;


                    }
                }

            }

            $last_queryColumnId[] = $queryColumnId ;
            $last_preSqlId[] = $preSqlId ;
            $last_orderColumnId[] = $orderColumnId ;
            $last_assignmentId[] = $assignmentId ;
            $last_CriteriaId[] = $CriteriaId ;
//            $last_QueryId[] = $QueryId ;
//            $last_QueryColumnId[] = $QueryColumnId;
//            $last_OrderColumnId[] = $OrderColumnId;
//            $last_AssignmentId[] = $AssignmentId;
            $last_CriteriaLinkId[] = $CriteriaLinkId;
            $last_PageHeaderId[] = $PageHeaderId;
            $last_PageFooterId[] = $PageFooterId;
            $last_DisplayOrderId[] = $DisplayOrderId;
            $last_GroupId[] = $GroupId;
            $last_GroupHeaderId[] = $GroupHeaderId;
            $last_GroupTrailerId[] = $GroupTrailerId;
            $last_GraphId[] = $GraphId;
            $last_PlotId[] = $PlotId;


            $last_CriteriaQueryId[] = $QueryId_UnderCriteria;
            $last_CriteriaQueryColumnId[] = $QueryColumnId_UnderCriteria;
            $last_CriteriaOrderColumnId[] = $OrderColumnId_UnderCriteria;
            $last_CriteriaAssignmentId[] = $AssignmentId_UnderCriteria;


            if($what == "Criteria")
            {

                if($i == (sizeof($data)-1))
                {
                    $tException = sizeof($newArray)-1 ;
                    //$tException =  $t ;
                    if(!empty($newArray))
                    {


//                        AppBasic::printRT($tException , 'QueryQueryId' );
//                        AppBasic::printRT($newArray[$tException]['Query']['QueryId'] , 'QueryQueryId' );
//                        AppBasic::printRT($newArray1[0]['QueryId'] , 'QueryId');

                        $QId = self::arrayKeyExist(0,$newArray1) ? (self::arrayKeyExist('QueryId',$newArray1[0])? $newArray1[0]['QueryId']:"") : "";
                        if(!empty($newArray1) && ($newArray[$tException]['Query']['QueryId'] == $QId) )
                        {
                            $newArray[$tException]['Query']['QueryColumns'] = $newArray1;
                        }

                        $QId = self::arrayKeyExist(0,$newArray1) ? (self::arrayKeyExist('QueryId',$newArray1[0])? $newArray1[0]['QueryId']:""): "";
                        if(!empty($newArray2) && ($newArray[$tException]['Query']['QueryId'] == $QId) )
                        {
                            $newArray[$tException]['Query']['OrderColumns'] = $newArray2;
                        }

                        $QId = self::arrayKeyExist(0,$newArray1) ? (self::arrayKeyExist('QueryId',$newArray1[0])? $newArray1[0]['QueryId']:""): "";
                        if(!empty($newArray3) && ($newArray[$tException]['Query']['QueryId'] == $QId) )
                        {
                            $newArray[$tException]['Query']['Assignments'] = $newArray3;
                        }
                    }
                }
            }

            if($what == "Criteria")
            {
                if($i == (sizeof($data)-1))
                {
                    //self::printRT($newArray1 , 'QueryColumns--NEWAARRAY!!!!!!' );
                    $tException =  sizeof($newArray)-1 ;
                    if(!empty($newArray))
                    {
//                        $newArray[$tException]['Query']['QueryColumns'] = $newArray1;
//                        $newArray[$tException]['Query']['OrderColumns'] = $newArray2;
//                        $newArray[$tException]['Query']['Assignments'] = $newArray3;
                        $newArray[$tException]['CriteriaLinks'] = $newArray4;
                    }
                }
            }

            if($what == "Output")
            {
                if($i == (sizeof($data)-1))
                {
                    $lException =  sizeof($newArray4)-1 ;
                    if(!empty($newArray4))
                    {
                        $newArray4[$lException]['GroupHeaders'] = $newArray5;
                        $newArray4[$lException]['GroupTrailers'] = $newArray6;
                    }

                    $oException =  sizeof($newArray7)-1 ;
                    if(!empty($newArray7))
                    {
                        $newArray7[$oException]['Plots'] = $newArray8;
                    }
                }
            }

        }


        if($what == "Output")
        {
            $newArray['PageHeaders'] = $newArray1 ;
            $newArray['PageFooters'] = $newArray2 ;
            $newArray['DisplayOrders'] = $newArray3 ;

            //$newArray4['GroupHeaders']['GroupHeader'] = $newArray5 ;
            //$newArray4['GroupTrailers']['GroupTrailer'] = $newArray6 ;
            $newArray['Groups'] = $newArray4 ;

            //$newArray7['Plots']['Plot'] = $newArray6 ;
            $newArray['Graphs'] = $newArray7 ;
        }



        if($what == "Criteria")
        {
//            self::printRT($newArray, 'newDataArray');
        }


        return $newArray;
    }


    public static function retrieveCoreReportFromDatabase($coreReportId)
    {

        $sql = '

            SELECT pageHeader.page_header_or_footer_id
                      AS pageHeader__PageHeaderOrFooterId,
                   pageHeader.core_report_id AS pageHeader__CoreReportId,
                   pageHeader.LineNumber AS pageHeader__LineNumber,
                   pageHeader.HeaderText AS pageHeader__HeaderText,
                   pageHeader.FooterText AS pageHeader__FooterText,
                   pageHeader.ColumnStartPDF AS pageHeader__ColumnStartPDF,
                   pageHeader.justify AS pageHeader__justify,
                   pageHeader.ColumnWidthPDF AS pageHeader__ColumnWidthPDF,
                   pageHeader.ShowInPDF AS pageHeader__ShowInPDF,
                   pageHeader.ShowInHTML AS pageHeader__ShowInHTML,
                   pageFooter.page_header_or_footer_id
                      AS pageFooter__PageHeaderOrFooterId,
                   pageFooter.core_report_id AS pageFooter__CoreReportId,
                   pageFooter.LineNumber AS pageFooter__LineNumber,
                   pageFooter.HeaderText AS pageFooter__HeaderText,
                   pageFooter.FooterText AS pageFooter__FooterText,
                   pageFooter.ColumnStartPDF AS pageFooter__ColumnStartPDF,
                   pageFooter.justify AS pageFooter__justify,
                   pageFooter.ColumnWidthPDF AS pageFooter__ColumnWidthPDF,
                   pageFooter.ShowInPDF AS pageFooter__ShowInPDF,
                   pageFooter.ShowInHTML AS pageFooter__ShowInHTML,
                   `plot`.plot_id AS plot__PlotId,
                   `plot`.graph_id AS plot__GraphId,
                   `plot`.PlotColumn AS plot__PlotColumn,
                   `plot`.PlotType AS plot__PlotType,
                   `plot`.LineColor AS plot__LineColor,
                   `plot`.DataType AS plot__DataType,
                   `plot`.Legend AS plot__Legend,
                   `plot`.FillColor AS plot__FillColor,
                   `group_trailer`.group_trailer_id AS group_trailer__GroupTrailerId,
                   `group_trailer`.group_id AS group_trailer__GroupId,
                   `group_trailer`.GroupTrailerDisplayColumn
                      AS group_trailer__GroupTrailerDisplayColumn,
                   `group_trailer`.GroupTrailerValueColumn
                      AS group_trailer__GroupTrailerValueColumn,
                   `group_trailer`.GroupTrailerCustom
                      AS group_trailer__GroupTrailerCustom,
                   `group_header`.group_header_id AS group_header__GroupHeaderId,
                   `group_header`.group_id AS group_header__GroupId,
                   `group_header`.GroupHeaderColumn AS group_header__GroupHeaderColumn,
                   `group_header`.GroupHeaderCustom AS group_header__GroupHeaderCustom,
                   `graph`.graph_id AS graph__GraphId,
                   `graph`.core_report_id AS graph__CoreReportId,
                   `graph`.GraphColumn AS graph__GraphColumn,
                   `graph`.GraphColor AS graph__GraphColor,
                   `graph`.Title AS graph__Title,
                   `graph`.GraphWidth AS graph__GraphWidth,
                   `graph`.GraphHeight AS graph__GraphHeight,
                   `graph`.GraphWidthPDF AS graph__GraphWidthPDF,
                   `graph`.GraphHeightPDF AS graph__GraphHeightPDF,
                   `graph`.XTitle AS graph__XTitle,
                   `graph`.YTitle AS graph__YTitle,
                   `graph`.GridPosition AS graph__GridPosition,
                   `graph`.XGridDisplay AS graph__XGridDisplay,
                   `graph`.XGridColor AS graph__XGridColor,
                   `graph`.YGridDisplay AS graph__YGridDisplay,
                   `graph`.YGridColor AS graph__YGridColor,
                   `graph`.XLabelColumn AS graph__XLabelColumn,
                   `graph`.TitleFont AS graph__TitleFont,
                   `graph`.TitleFontStyle AS graph__TitleFontStyle,
                   `graph`.TitleFontSize AS graph__TitleFontSize,
                   `graph`.TitleColor AS graph__TitleColor,
                   `graph`.XTitleFont AS graph__XTitleFont,
                   `graph`.XTitleFontStyle AS graph__XTitleFontStyle,
                   `graph`.XTitleFontSize AS graph__XTitleFontSize,
                   `graph`.XTitleColor AS graph__XTitleColor,
                   `graph`.YTitleFont AS graph__YTitleFont,
                   `graph`.YTitleFontStyle AS graph__YTitleFontStyle,
                   `graph`.YTitleFontSize AS graph__YTitleFontSize,
                   `graph`.YTitleColor AS graph__YTitleColor,
                   `graph`.XAxisColor AS graph__XAxisColor,
                   `graph`.XAxisFont AS graph__XAxisFont,
                   `graph`.XAxisFontStyle AS graph__XAxisFontStyle,
                   `graph`.XAxisFontSize AS graph__XAxisFontSize,
                   `graph`.XAxisFontColor AS graph__XAxisFontColor,
                   `graph`.YAxisColor AS graph__YAxisColor,
                   `graph`.YAxisFont AS graph__YAxisFont,
                   `graph`.YAxisFontStyle AS graph__YAxisFontStyle,
                   `graph`.YAxisFontSize AS graph__YAxisFontSize,
                   `graph`.YAxisFontColor AS graph__YAxisFontColor,
                   `graph`.XTickInterval AS graph__XTickInterval,
                   `graph`.YTickInterval AS graph__YTickInterval,
                   `graph`.XTickLabelInterval AS graph__XTickLabelInterval,
                   `graph`.YTickLabelInterval AS graph__YTickLabelInterval,
                   `graph`.MarginColor AS graph__MarginColor,
                   `graph`.MarginLeft AS graph__MarginLeft,
                   `graph`.MarginRight AS graph__MarginRight,
                   `graph`.MarginTop AS graph__MarginTop,
                   `graph`.MarginBottom AS graph__MarginBottom,
                   `display_order`.display_order_id AS display_order__DisplayOrderId,
                   `display_order`.core_report_id AS display_order__CoreReportId,
                   `display_order`.ColumnName AS display_order__ColumnName,
                   `display_order`.OrderNumber AS display_order__OrderNumber,
                   `data_source`.data_source_id AS data_source__DataSourceId,
                   `data_source`.core_report_id AS data_source__CoreReportId,
                   `data_source`.SourceType AS data_source__SourceType,
                   `data_source`.DatabaseType AS data_source__DatabaseType,
                   `data_source`.DatabaseName AS data_source__DatabaseName,
                   `data_source`.HostName AS data_source__HostName,
                   `data_source`.ServiceName AS data_source__ServiceName,
                   `data_source`.UserName AS data_source__UserName,
                   `data_source`.Password AS data_source__Password,
                   `core_reports`.core_report_id AS core_reports__CoreReportId,
                   `core_reports`.report_type AS core_reports__ReportType,
                    CASE
                          WHEN `core_reports`.is_client_specific = 1 THEN "YES"
                          WHEN `core_reports`.is_client_specific = 0 THEN "NO"
                    ELSE "NO"
                    END
                    AS core_reports__is_client_specific,
                   `core_reports`.industry_database_id
                      AS core_reports__industry_database_id,
                   `core_reports`.ReportTitle AS core_reports__ReportTitle,
                   `core_reports`.report_name AS core_reports__ReportName,
                   `core_reports`.ReportDescription AS core_reports__ReportDescription,
                   `core_reports`.PageSize AS core_reports__PageSize,
                   `core_reports`.PageOrientation AS core_reports__PageOrientation,
                   `core_reports`.TopMargin AS core_reports__TopMargin,
                   `core_reports`.BottomMargin AS core_reports__BottomMargin,
                   `core_reports`.RightMargin AS core_reports__RightMargin,
                   `core_reports`.LeftMargin AS core_reports__LeftMargin,
                   `core_reports`.pdfFont AS core_reports__pdfFont,
                   `core_reports`.pdfFontSize AS core_reports__pdfFontSize,
                   `core_reports`.PreExecuteCode AS core_reports__PreExecuteCode,
                   `core_reports`.formBetweenRows AS core_reports__formBetweenRows,
                   `core_reports`.gridDisplay AS core_reports__gridDisplay,
                   `core_reports`.gridSortable AS core_reports__gridSortable,
                   `core_reports`.gridSearchable AS core_reports__gridSearchable,
                   `core_reports`.gridPageable AS core_reports__gridPageable,
                   `core_reports`.gridPageSize AS core_reports__gridPageSize,
                   `core_reports`.SourceType AS core_reports__SourceType,
                   `core_reports`.TableSql AS core_reports__TableSql,
                   `core_reports`.WhereSql AS core_reports__WhereSql,
                   `core_reports`.GroupSql AS core_reports__GroupSql,
                   `core_reports`.RowSelection AS core_reports__RowSelection,
                   `core_reports`.SQLRaw AS core_reports__SQLRaw,
                   `assignments`.assignments_id AS assignments__AssignmentId,
                   `assignments`.core_report_id AS assignments__CoreReportId,
                   `assignments`.AssignName AS assignments__AssignName,
                   `assignments`.AssignNameNew AS assignments__AssignNameNew,
                   `assignments`.Expression AS assignments__Expression,
                   `assignments`.`Condition` AS assignments__Condition,
                   `assignments`.query_id AS assignments__QueryId,
                   group_tbl.group_id AS group_tbl__GroupId,
                   group_tbl.core_report_id AS group_tbl__CoreReportId,
                   group_tbl.GroupName AS group_tbl__GroupName,
                   group_tbl.BeforeGroupHeader AS group_tbl__BeforeGroupHeader,
                   group_tbl.AfterGroupHeader AS group_tbl__AfterGroupHeader,
                   group_tbl.BeforeGroupTrailer AS group_tbl__BeforeGroupTrailer,
                   group_tbl.AfterGroupTrailer AS group_tbl__AfterGroupTrailer
              FROM core_reports
                   LEFT JOIN data_source
                      ON data_source.core_report_id = core_reports.core_report_id
                   LEFT JOIN assignments
                      ON assignments.core_report_id = core_reports.core_report_id
                   LEFT JOIN page_header_or_footer AS pageHeader
                      ON     pageHeader.core_report_id = core_reports.core_report_id
                        AND  pageHeader.FooterText IS NULL
                   LEFT JOIN page_header_or_footer AS pageFooter
                      ON     pageFooter.core_report_id = core_reports.core_report_id
                        AND  pageFooter.HeaderText IS NULL
                   LEFT JOIN display_order
                      ON display_order.core_report_id = core_reports.core_report_id
                   LEFT JOIN group_tbl
                      ON group_tbl.core_report_id = core_reports.core_report_id
                   LEFT JOIN group_header ON group_header.group_id = group_tbl.group_id
                   LEFT JOIN group_trailer ON group_trailer.group_id = group_tbl.group_id
                   LEFT JOIN graph ON graph.core_report_id = core_reports.core_report_id
                   LEFT JOIN plot ON plot.graph_id = graph.graph_id
             WHERE core_reports.core_report_id = :core_report_id
            ORDER BY core_reports.core_report_id,
                     data_source.data_source_id,
                     assignments.assignments_id,
                     pageHeader.page_header_or_footer_id,
                     pageFooter.page_header_or_footer_id,
                     display_order.display_order_id,
                     group_tbl.group_id,
                     group_header.group_header_id,
                     group_trailer.group_trailer_id,
                     graph.graph_id,
                     plot.plot_id

        ';

        $command = \Yii::$app->db->createCommand($sql, [':core_report_id'=>$coreReportId]);
        $data = $command->queryAll();

        return $data;
    }

    public static function retrieveQueryFromDatabase($coreReportId)
    {

        $sql = '
                        SELECT `pre_sql`.pre_sql_id AS pre_sql__PreSqlId,
                               `pre_sql`.query_id AS pre_sql__QueryId,
                               `pre_sql`.SQLText AS pre_sql__SQLText,
                               `query`.query_id AS query__QueryId,
                               `query`.core_report_id AS query__CoreReportId,
                               `query`.criteria_id AS query__CriteriaId,
                               `query`.TableSql AS query__TableSql,
                               `query`.WhereSql AS query__WhereSql,
                               `query`.GroupSql AS query__GroupSql,
                               `query`.RowSelection AS query__RowSelection,
                               `query`.QuerySql AS query__QuerySql,
                               `query`.SQLRaw AS query__SQLRaw,
                               `query_column`.query_column_id AS query_column__QueryColumnId,
                               `query_column`.query_id AS query_column__QueryId,
                               `query_column`.Name AS query_column__Name,
                               `query_column`.TableName AS query_column__TableName,
                               `query_column`.ColumnName AS query_column__ColumnName,
                               `query_column`.ColumnType AS query_column__ColumnType,
                               `query_column`.ColumnLength AS query_column__ColumnLength,
                               `query_column`.column_display AS query_column__column_display,
                               `query_column`.content_type AS query_column__content_type,
                               `query_column`.ColumnStartPDF AS query_column__ColumnStartPDF,
                               `query_column`.justify AS query_column__justify,
                               `query_column`.ColumnWidthPDF AS query_column__ColumnWidthPDF,
                               `query_column`.ColumnWidthHTML AS query_column__ColumnWidthHTML,
                               `query_column`.column_title AS query_column__column_title,
                               `query_column`.tooltip AS query_column__tooltip,
                               `query_column`.group_header_label AS query_column__group_header_label,
                               `query_column`.group_header_label_xpos
                                  AS query_column__group_header_label_xpos,
                               `query_column`.group_header_data_xpos
                                  AS query_column__group_header_data_xpos,
                               `query_column`.group_trailer_label
                                  AS query_column__group_trailer_label,
                               `order_column`.order_column_id AS order_column__OrderColumnId,
                               `order_column`.query_id AS order_column__QueryId,
                               `order_column`.Name AS order_column__Name,
                               `order_column`.OrderType AS order_column__OrderType
                          FROM query
                               LEFT JOIN query_column ON query.query_id = query_column.query_id
                               LEFT JOIN order_column ON order_column.query_id = query.query_id
                               LEFT JOIN pre_sql ON pre_sql.query_id = query.query_id
                         WHERE query.core_report_id = :core_report_id
                        ORDER BY query.query_id,
                                 query_column.query_column_id,
                                 order_column.order_column_id,
                                 pre_sql.pre_sql_id

        ';

        $command = \Yii::$app->db->createCommand($sql, [':core_report_id'=>$coreReportId]);
        $data = $command->queryAll();

        return $data;
    }

    public static function retrieveCriteriaFromDatabase($coreReportId)
    {
        $sql = '

            SELECT
                   `criteria`.criteria_id AS criteria__CriteriaId,
                   `criteria`.core_report_id AS criteria__CoreReportId,
                   `criteria`.Name AS criteria__Name,
                   `criteria`.Title AS criteria__Title,
                   `criteria`.QueryTableName AS criteria__QueryTableName,
                   `criteria`.QueryColumnName AS criteria__QueryColumnName,
                   `criteria`.CriteriaType AS criteria__CriteriaType,
                   `criteria`.CriteriaDisplay AS criteria__CriteriaDisplay,
                   `criteria`.ExpandDisplay AS criteria__ExpandDisplay,
                   `criteria`.ReturnColumn AS criteria__ReturnColumn,
                   `criteria`.DisplayColumn AS criteria__DisplayColumn,
                   `criteria`.OverviewColumn AS criteria__OverviewColumn,
                   `criteria`.MatchColumn AS criteria__MatchColumn,
                   `criteria`.CriteriaDefaults AS criteria__CriteriaDefaults,
                   `criteria`.CriteriaList AS criteria__CriteriaList,
                   queryUnderCriteria.query_id AS queryUnderCriteria__QueryId,
                   queryUnderCriteria.core_report_id AS queryUnderCriteria__CoreReportId,
                   queryUnderCriteria.criteria_id AS queryUnderCriteria__CriteriaId,
                   queryUnderCriteria.TableSql AS queryUnderCriteria__TableSql,
                   queryUnderCriteria.WhereSql AS queryUnderCriteria__WhereSql,
                   queryUnderCriteria.GroupSql AS queryUnderCriteria__GroupSql,
                   queryUnderCriteria.RowSelection AS queryUnderCriteria__RowSelection,
                   queryUnderCriteria.QuerySql AS queryUnderCriteria__QuerySql,
                   queryUnderCriteria.SQLRaw AS queryUnderCriteria__SQLRaw,
                   queryColumnUnderCriteria.query_column_id
                      AS queryColumnUnderCriteria__QueryColumnId,
                   queryColumnUnderCriteria.query_id AS queryColumnUnderCriteria__QueryId,
                   queryColumnUnderCriteria.Name AS queryColumnUnderCriteria__Name,
                   queryColumnUnderCriteria.TableName
                      AS queryColumnUnderCriteria__TableName,
                   queryColumnUnderCriteria.ColumnName
                      AS queryColumnUnderCriteria__ColumnName,
                   queryColumnUnderCriteria.ColumnType
                      AS queryColumnUnderCriteria__ColumnType,
                   queryColumnUnderCriteria.ColumnLength
                      AS queryColumnUnderCriteria__ColumnLength,
                   queryColumnUnderCriteria.column_display
                      AS queryColumnUnderCriteria__column_display,
                   queryColumnUnderCriteria.content_type
                      AS queryColumnUnderCriteria__content_type,
                   queryColumnUnderCriteria.ColumnStartPDF
                      AS queryColumnUnderCriteria__ColumnStartPDF,
                   queryColumnUnderCriteria.justify AS queryColumnUnderCriteria__justify,
                   queryColumnUnderCriteria.ColumnWidthPDF
                      AS queryColumnUnderCriteria__ColumnWidthPDF,
                   queryColumnUnderCriteria.ColumnWidthHTML
                      AS queryColumnUnderCriteria__ColumnWidthHTML,
                   queryColumnUnderCriteria.column_title
                      AS queryColumnUnderCriteria__column_title,
                   queryColumnUnderCriteria.tooltip AS queryColumnUnderCriteria__tooltip,
                   queryColumnUnderCriteria.group_header_label
                      AS queryColumnUnderCriteria__group_header_label,
                   queryColumnUnderCriteria.group_header_label_xpos
                      AS queryColumnUnderCriteria__group_header_label_xpos,
                   queryColumnUnderCriteria.group_header_data_xpos
                      AS queryColumnUnderCriteria__group_header_data_xpos,
                   queryColumnUnderCriteria.group_trailer_label
                      AS queryColumnUnderCriteria__group_trailer_label,
                   orderColumnUnderCriteria.order_column_id
                      AS orderColumnUnderCriteria__OrderColumnId,
                   orderColumnUnderCriteria.query_id AS orderColumnUnderCriteria__QueryId,
                   orderColumnUnderCriteria.Name AS orderColumnUnderCriteria__Name,
                   orderColumnUnderCriteria.OrderType
                      AS orderColumnUnderCriteria__OrderType,
                   assignmentsUnderCriteria.assignments_id
                      AS assignmentsUnderCriteria__AssignmentId,
                   assignmentsUnderCriteria.core_report_id
                      AS assignmentsUnderCriteria__CoreReportId,
                   assignmentsUnderCriteria.AssignName
                      AS assignmentsUnderCriteria__AssignName,
                   assignmentsUnderCriteria.AssignNameNew
                      AS assignmentsUnderCriteria__AssignNameNew,
                   assignmentsUnderCriteria.Expression
                      AS assignmentsUnderCriteria__Expression,
                   assignmentsUnderCriteria.query_id AS assignmentsUnderCriteria__QueryId,
                   assignmentsUnderCriteria.Condition AS assignmentsUnderCriteria__Condition,
                   `criteria_links`.criteria_link_id AS criteria_links__CriteriaLinkId,
                   `criteria_links`.criteria_id AS criteria_links__criteria_id,
                   `criteria_links`.LinkFrom AS criteria_links__LinkFrom,
                   `criteria_links`.LinkTo AS criteria_links__LinkTo,
                   `criteria_links`.LinkClause AS criteria_links__LinkClause
              FROM criteria
                   LEFT JOIN query AS queryUnderCriteria
                      ON queryUnderCriteria.criteria_id = criteria.criteria_id
                   LEFT JOIN query_column AS queryColumnUnderCriteria
                      ON queryUnderCriteria.query_id = queryColumnUnderCriteria.query_id
                   LEFT JOIN order_column AS orderColumnUnderCriteria
                      ON orderColumnUnderCriteria.query_id = queryUnderCriteria.query_id
                   LEFT JOIN assignments AS assignmentsUnderCriteria
                      ON assignmentsUnderCriteria.query_id = queryUnderCriteria.query_id
                   LEFT JOIN criteria_links
                      ON criteria_links.criteria_id = criteria.criteria_id
             WHERE criteria.core_report_id = :core_report_id
             ORDER BY criteria.criteria_id,
                     queryUnderCriteria.query_id,
                     queryColumnUnderCriteria.query_column_id,
                     orderColumnUnderCriteria.order_column_id,
                     assignmentsUnderCriteria.assignments_id,
                     criteria_links.criteria_link_id

        ';

        $command = \Yii::$app->db->createCommand($sql, [':core_report_id'=>$coreReportId]);
        $data = $command->queryAll();

        return $data;
    }

    public static function getArrayInnerKey($property , $key1, $key2 = null, $key3 = null, $key4 = null)
    {
         $array = [
             "data_source"  => ["DatabaseType","DatabaseName","HostName","ServiceName","UserName","Password"],
             "core_reports" => [ "is_client_specific","industry_database_id",  "ReportType","ReportTitle","ReportDescription","PageSize","PageOrientation","TopMargin","BottomMargin",
                        "RightMargin","LeftMargin","pdfFont","pdfFontSize","PreExecuteCode","formBetweenRows","gridDisplay",
                        "gridSortable","gridSearchable","gridPageable","gridPageSize","CoreReportId","ReportName"],
             "query"  => ["QuerySql","SQLRaw"],
             "queryUnderCriteria"  => ["QuerySql"],
             "pageHeader"  => ["ColumnStartPDF","justify","ColumnWidthPDF","ShowInPDF","ShowInHTML"],
         ];



         $k1 = self::arrayKeyExist($key1, $array, 0) ;
         $k2 = $k1 ?  self::arrayKeyExist($key2, $array[$key1], 0)  : 0 ;
         $k3 = $k2 ?  self::arrayKeyExist($key3, $array[$key1][$key2], 0)  : 0 ;
         $k4 = $k3 ?  self::arrayKeyExist($key4, $array[$key1][$key2][$key3], 0)  : 0 ;

         if(self::stringNotNull($key4) && self::stringNotNull($key3) && self::stringNotNull($key2) && self::stringNotNull($key1)  &&  $k4)
         {
              return in_array($property, $array[$key1][$key2][$key3][$key4]);
         }
         else if(self::stringNotNull($key3) && self::stringNotNull($key2) && self::stringNotNull($key1)  && $k3 )
         {
             return in_array($property, $array[$key1][$key2][$key3]);
         }
         else if(self::stringNotNull($key2) && self::stringNotNull($key1) && $k2)
         {
             return in_array($property, $array[$key1][$key2]);
         }
         else if(self::stringNotNull($key1) && $k1)
         {
             return in_array($property, $array[$key1]);
         }
         else
         {
             return false;
         }

    }

    public static function getAllCoreReports()
    {
       //require_once(__DIR__."/../../vendor/reportico/yii2-reportico/components/swutil.php");
      $projectId = self::get_reportico_session_param('project');
      //AppBasic::printRT($projectId , 'PROJECTID' );

      $clientDataBase = ClientDatabases::findOne($projectId);
      if($clientDataBase != null)
      {
          $data =  CoreReports::find()->where('industry_database_id=:industry_database_id', [':industry_database_id'=>$clientDataBase->industry_database_id] )->all() ;
      }
      else
      {
          trigger_error("There is no project selected. Or seleted Project do not have correct Industry Database Value.");
      }

      return $data;

    }

    public static function registerClientDatabase($clientDatabaseId)
    {
        //self::printRT($clientDatabaseId , "clientDatabaseId");
        $ClientDatabase = ClientDatabases::findOne($clientDatabaseId);
        //self::printRT( $ClientDatabase->attributes , "ClientDatabase" );


        if($ClientDatabase != null)
        {

            foreach ($ClientDatabase->attributes as $k => $val) {
                $configName = ClientDatabases::getConfigName($k);
                if ($configName != null && $configName != "")
                {
                    self::redefineConstants($configName, $val);
                    //define($configName, $val);
//                    if(defined($configName))
//                    {
//                        runkit_constant_redefine($configName, $val);
//                    }
//                    else
//                    {
//                        define($configName, $val);
//                    }
                }
            }

            //AppBasic::printR(SW_CLIENT_DATABASE_ID, 0);
            //AppBasic::printR(SW_CLIENT_ID, 0);
            //AppBasic::printR(SW_DB_HOST, 0);
//            AppBasic::printR(SW_DB_USER, 0);
//            AppBasic::printR(SW_DB_PASSWORD, 0);
            //AppBasic::printR(SW_PROJECT_PASSWORD, 0);


            // Location of Reportico Top Level Directory From Browser Point of View
            self::redefineConstants('SW_HTTP_URLHOST', 'http://127.0.0.1');
            self::redefineConstants('SW_DEFAULT_PROJECT', 'reports');

            // Project Title used at the top of menus
            //self::redefineConstants('SW_PROJECT_TITLE', 'Client Database - ' . $ClientDatabase->client_database_id);
            self::redefineConstants('SW_PROJECT_TITLE',  'Client Database - ' . $ClientDatabase->client_database_id);

            // Identify whether to always run in into Debug Mode
            self::redefineConstants('SW_ALLOW_OUTPUT', true);
            self::redefineConstants('SW_ALLOW_DEBUG', true);

            // Identify whether Show Criteria is default option
            self::redefineConstants('SW_DEFAULT_SHOWCRITERIA', false);

            // If false prevents any designing of reports
            self::redefineConstants('SW_ALLOW_MAINTAIN', true);

            // Identify whether to use AJAX handling. Enabling with enable Data Pickers,
            // loading of partial form elements and quicker-ti-use design mode
            self::redefineConstants('AJAX_ENABLED', true);


            $driverName = DatabaseType::findOne($ClientDatabase->database_type_id);
            if($driverName != null)
            {
                self::redefineConstants('SW_DB_DRIVER', $driverName->driver);
                self::redefineConstants('SW_DB_TYPE', $driverName->driver);
            }
            else
            {
                trigger_error("Database sriver can not be empty.");
            }



            //AppBasic::printRT($ClientDatabase->client_database_id , "client_database_id");
            //AppBasic::printRT($ClientDatabase->db_character_encoding_id , "SW_DB_ENCODING");
            //AppBasic::printRT($ClientDatabase->language_id , "SW_LANGUAGE");
            //AppBasic::printRT($ClientDatabase->output_character_encoding_id , "SW_OUTPUT_ENCODING");
            //AppBasic::printRT($ClientDatabase->display_date_format_id , "SW_PREP_DATEFORMAT");
            //AppBasic::printRT($ClientDatabase->database_date_format_id , "SW_DB_DATEFORMAT");


            $outputEncoding = OutputCharacterEncoding::findOne($ClientDatabase->output_character_encoding_id);
            self::redefineConstants( 'SW_OUTPUT_ENCODING' , $outputEncoding->encoding_type );

            $dbEncoding = DbCharacterEncoding::findOne($ClientDatabase->db_character_encoding_id);
            self::redefineConstants( 'SW_DB_ENCODING' , $dbEncoding->encoding_type );

            $displayDate = DisplayDateFormat::findOne($ClientDatabase->display_date_format_id);
            self::redefineConstants( 'SW_PREP_DATEFORMAT' , $displayDate->display_format );

            $displayDate = DisplayDateFormat::findOne($ClientDatabase->database_date_format_id);
            self::redefineConstants( 'SW_DB_DATEFORMAT' , $displayDate->display_format );

            $languages = Languages::findOne($ClientDatabase->language_id);
            self::redefineConstants( 'SW_LANGUAGE' , $languages->value );


            //AppBasic::printRT(SW_DB_ENCODING , "SW_DB_ENCODING");
            //AppBasic::printRT(SW_LANGUAGE , "SW_LANGUAGE");
            //AppBasic::printRT(SW_OUTPUT_ENCODING , "SW_OUTPUT_ENCODING");
            //AppBasic::printRT(SW_PREP_DATEFORMAT , "SW_PREP_DATEFORMAT");
            ///AppBasic::printRT(SW_DB_DATEFORMAT , "SW_DB_DATEFORMAT");


            self::redefineConstants('SW_DB_CONNECT_FROM_CONFIG', true);
            self::redefineConstants('SW_DB_CONNECT_FROM_SESSION', true);

            // Identify temp areaSW_DB_CONNECT_FROM_CONFIG
            self::redefineConstants('SW_TMP_DIR', "tmp");


            // SOAP Environment
            self::redefineConstants('SW_SOAP_NAMESPACE', 'reportico.org');
            self::redefineConstants('SW_SOAP_SERVICEBASEURL', 'http://www.reportico.co.uk/swsite/site/tutorials');

            // Parameter Defaults
            self::redefineConstants('SW_DEFAULT_PageSize', 'A4');
            self::redefineConstants('SW_DEFAULT_PageOrientation', 'Portrait');
            self::redefineConstants('SW_DEFAULT_TopMargin', "1cm");
            self::redefineConstants('SW_DEFAULT_BottomMargin', "2cm");
            self::redefineConstants('SW_DEFAULT_LeftMargin', "1cm");
            self::redefineConstants('SW_DEFAULT_RightMargin', "1cm");
            self::redefineConstants('SW_DEFAULT_pdfFont', "Helvetica");
            self::redefineConstants('SW_DEFAULT_pdfFontSize', "10");

            // FPDF parameters
            self::redefineConstants('FPDF_FONTPATH', 'fpdf/font/');

            // Include an image in your PDF output
            // This defalt places icon top right of a portrait image and sizes it to 100 pixels wide
            //self::redefineConstants('PDF_HEADER_IMAGE', 'images/myimage.png');
            //self::redefineConstants('PDF_HEADER_XPOS', '470');
            //self::redefineConstants('PDF_HEADER_YPOS', '20');
            //self::redefineConstants('PDF_HEADER_WIDTH', '100');

            // Graph Defaults
            // Default Charting Engine is JpGraph. A slightly modified version 3.0.7 of jpGraph is supplied
            // within Reportico.

            // Reportico also supports pChart but the pChart package is not currently provided
            // as part of the Reportico bundle. To use pChart you will need to unpack the pChart
            // application into the reportico folder named pChart. pChart 2.1.3
            // You can get pChart from http://www.pchart.net/

            self::redefineConstants("SW_GRAPH_ENGINE", "PCHART");
            if (!defined("SW_GRAPH_ENGINE") || SW_GRAPH_ENGINE == "JPGRAPH") {
                self::redefineConstants('SW_DEFAULT_Font', "Arial");
//advent_light
//Bedizen
//Mukti_Narrow
//calibri
//Forgotte
//GeosansLight
//MankSans
//pf_arma_five
//Silkscreen
//verdana
                self::redefineConstants('SW_DEFAULT_GraphWidth', 800);
                self::redefineConstants('SW_DEFAULT_GraphHeight', 400);
                self::redefineConstants('SW_DEFAULT_GraphWidthPDF', 500);
                self::redefineConstants('SW_DEFAULT_GraphHeightPDF', 250);
                self::redefineConstants('SW_DEFAULT_GraphColor', "white");
                self::redefineConstants('SW_DEFAULT_MarginTop', "40");
                self::redefineConstants('SW_DEFAULT_MarginBottom', "90");
                self::redefineConstants('SW_DEFAULT_MarginLeft', "60");
                self::redefineConstants('SW_DEFAULT_MarginRight', "50");
                self::redefineConstants('SW_DEFAULT_MarginColor', "white");
                self::redefineConstants('SW_DEFAULT_XTickLabelInterval', "1");
                self::redefineConstants('SW_DEFAULT_YTickLabelInterval', "2");
                self::redefineConstants('SW_DEFAULT_XTickInterval', "1");
                self::redefineConstants('SW_DEFAULT_YTickInterval', "1");
                self::redefineConstants('SW_DEFAULT_GridPosition', "back");
                self::redefineConstants('SW_DEFAULT_XGridDisplay', "none");
                self::redefineConstants('SW_DEFAULT_XGridColor', "gray");
                self::redefineConstants('SW_DEFAULT_YGridDisplay', "none");
                self::redefineConstants('SW_DEFAULT_YGridColor', "gray");
                self::redefineConstants('SW_DEFAULT_TitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_TitleFontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_TitleFontSize', "12");
                self::redefineConstants('SW_DEFAULT_TitleColor', "black");
                self::redefineConstants('SW_DEFAULT_XTitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_XTitleFontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_XTitleFontSize', "10");
                self::redefineConstants('SW_DEFAULT_XTitleColor', "black");
                self::redefineConstants('SW_DEFAULT_YTitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_YTitleFontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_YTitleFontSize', "10");
                self::redefineConstants('SW_DEFAULT_YTitleColor', "black");
                self::redefineConstants('SW_DEFAULT_XAxisFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_XAxisFontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_XAxisFontSize', "10");
                self::redefineConstants('SW_DEFAULT_XAxisFontColor', "black");
                self::redefineConstants('SW_DEFAULT_XAxisColor', "black");
                self::redefineConstants('SW_DEFAULT_YAxisFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_YAxisFontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_YAxisFontSize', "8");
                self::redefineConstants('SW_DEFAULT_YAxisFontColor', "black");
                self::redefineConstants('SW_DEFAULT_YAxisColor', "black");
            } else // Use jpgraph
            {
                self::redefineConstants('SW_DEFAULT_Font', "Mukti_Narrow.ttf");

                //advent_light.ttf
                //Bedizen.ttf
                //calibri.ttf
                //Forgotte.ttf
                //GeosansLight.ttf
                //MankSans.ttf
                //pf_arma_five.ttf
                //Silkscreen.ttf
                //verdana.ttf

                self::redefineConstants('SW_DEFAULT_FontSize', "8");
                self::redefineConstants('SW_DEFAULT_FontColor', "#303030");
                self::redefineConstants('SW_DEFAULT_LineColor', "#303030");
                self::redefineConstants('SW_DEFAULT_BackColor', "#eeeeff");
                self::redefineConstants('SW_DEFAULT_FontStyle', "Normal");
                self::redefineConstants('SW_DEFAULT_GraphWidth', 800);
                self::redefineConstants('SW_DEFAULT_GraphHeight', 400);
                self::redefineConstants('SW_DEFAULT_GraphWidthPDF', 500);
                self::redefineConstants('SW_DEFAULT_GraphHeightPDF', 300);
                self::redefineConstants('SW_DEFAULT_GraphColor', SW_DEFAULT_BackColor);
                self::redefineConstants('SW_DEFAULT_MarginTop', "50");
                self::redefineConstants('SW_DEFAULT_MarginBottom', "80");
                self::redefineConstants('SW_DEFAULT_MarginLeft', "70");
                self::redefineConstants('SW_DEFAULT_MarginRight', "40");
                self::redefineConstants('SW_DEFAULT_MarginColor', SW_DEFAULT_BackColor);
                self::redefineConstants('SW_DEFAULT_XTickLabelInterval', "AUTO");
                self::redefineConstants('SW_DEFAULT_YTickLabelInterval', "2");
                self::redefineConstants('SW_DEFAULT_XTickInterval', "1");
                self::redefineConstants('SW_DEFAULT_YTickInterval', "1");
                self::redefineConstants('SW_DEFAULT_GridPosition', "back");
                self::redefineConstants('SW_DEFAULT_XGridDisplay', "none");
                self::redefineConstants('SW_DEFAULT_XGridColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_YGridDisplay', "none");
                self::redefineConstants('SW_DEFAULT_YGridColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_TitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_TitleFontStyle', SW_DEFAULT_FontStyle);
                self::redefineConstants('SW_DEFAULT_TitleFontSize', 12);
                self::redefineConstants('SW_DEFAULT_TitleColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_XTitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_XTitleFontStyle', SW_DEFAULT_FontStyle);
                self::redefineConstants('SW_DEFAULT_XTitleFontSize', SW_DEFAULT_FontSize);
                self::redefineConstants('SW_DEFAULT_XTitleColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_YTitleFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_YTitleFontStyle', SW_DEFAULT_FontStyle);
                self::redefineConstants('SW_DEFAULT_YTitleFontSize', SW_DEFAULT_FontSize);
                self::redefineConstants('SW_DEFAULT_YTitleColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_XAxisFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_XAxisFontStyle', SW_DEFAULT_FontStyle);
                self::redefineConstants('SW_DEFAULT_XAxisFontSize', SW_DEFAULT_FontSize);
                self::redefineConstants('SW_DEFAULT_XAxisFontColor', SW_DEFAULT_FontColor);
                self::redefineConstants('SW_DEFAULT_XAxisColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_YAxisFont', SW_DEFAULT_Font);
                self::redefineConstants('SW_DEFAULT_YAxisFontStyle', SW_DEFAULT_FontStyle);
                self::redefineConstants('SW_DEFAULT_YAxisFontSize', SW_DEFAULT_FontSize);
                self::redefineConstants('SW_DEFAULT_YAxisFontColor', SW_DEFAULT_LineColor);
                self::redefineConstants('SW_DEFAULT_YAxisColor', SW_DEFAULT_LineColor);

            }

            $menu_title = SW_PROJECT_TITLE;
            $menu = array(
                array("language" => "en_gb", "report" => ".*\.xml", "title" => "<AUTO>")
            );

            //$menu = CreateModule::findAll();


            $g_project = false;
            $g_projpath = false;
            $g_menu = false;
            $g_menu_title = "";
            $g_dropdown_menu = false;
            $old_error_handler = set_error_handler("\\common\\modules\\reporticom\\components\\ErrorHandler");

        }
        else
        {
            trigger_error(" Client Database can not be null");
        }

    }

    public static function redefineConstants($CONST, $val)
    {
        if(defined($CONST))
        {
            \Yii::$app->session->set($CONST,$val);
        }
        else
        {
            \Yii::$app->session->set($CONST,$val);
            define($CONST, $val);
        }
    }

    public static function getIndustryDatabaseIds()
    {
       $array = ArrayHelper::map(  IndustryDatabase::find()->all() , 'industry_database_id' , 'name' );
       //self::printRT($array);
       return $array ;
    }

    public static function getRightModel($model , $id , $searchModel)
    {
       if(!$searchModel)
       {
           if(self::stringNotNull($id))
           {
               $model1 = $model->findOne($id);
               if($model1 !== null)
               {
                   return $model1 ;
               }
           }
       }

       return $model ;
    }

    public static function returnModel($model_name, $id = null, $searchModel = false)
    {
        $classFile = __DIR__ . "/../models/".$model_name.".php";

        if(!is_dir($classFile) && is_file($classFile))
        {
            require_once  $classFile;

            $model_name = "common\\models\\".$model_name ;
            $model =  new $model_name();
            $model = self::getRightModel($model , $id, $searchModel);

            return $model;
        }
        else
        {
            trigger_error("Model Class is not present. Please debug error manually.");
        }
    }

    public static function classListByDirectory($dir = null , $sequential = 1, $ByClassName = 1)
    {

        $dir = AppBasic::stringNotNull($dir) ? $dir :  __DIR__ ;
        $data = scandir($dir);

        $newData =  array();
        for($i=0;  $i<sizeof($data); $i++)
        {
            if(strpos($data[$i] , ".php") !== false)
            {
                $str = str_replace(".php","",$data[$i]);
                if($ByClassName)
                {
                    $finalName  = $str ;
                    if(AppBasic::stringNotNull($finalName))
                    {
                        if($sequential)
                        {
                            $newData[] = $finalName;
                        }
                        else
                        {
                            $newData[$finalName] = $finalName;
                        }
                    }
                }
                else
                {
                    $pieces = preg_split('/(?=[A-Z])/',$str);
                    $finalName = "" ;
                    for($j=0; $j < sizeof($pieces); $j++)
                    {
                        if(AppBasic::stringNotNull($pieces[$j]))
                        {
                            $finalName .= strtoupper($pieces[$j]);

                            if($finalName != "" && $j != (sizeof($pieces)-1))
                            {
                                $finalName .= "_";
                            }
                        }
                    }

                    if(AppBasic::stringNotNull($finalName))
                    {
                        if($sequential)
                        {
                            $newData[] = "FORM_".$finalName;
                            $newData[] = "ADMIN_".$finalName;
                        }
                        else
                        {
                            $newData["FORM_".$finalName] = "FORM_".$finalName;
                            $newData["ADMIN_".$finalName] = "ADMIN_".$finalName;
                        }
                    }
                }
            }
        }

        return $newData ;
    }

    public static function checkTableExist($tblName)
    {
        if(self::stringNotNull($tblName))
        {

            $sql = "
            SHOW tables LIKE '".$tblName."';
        " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $return = $conn->queryAll();

            return self::arrayNotEmpty($return) ;

        }
        else
        {
            return false;
        }
    }

    public static function checkColumnExistInTable($columnName , $tblName)
    {
        if(self::stringNotNull($tblName) && self::stringNotNull($columnName))
        {
            $sql = ' SHOW COLUMNS FROM '.$tblName.' WHERE Field =  "'.$columnName.'"' ;

            $conn = \Yii::$app->db->createCommand($sql);
            $return = $conn->queryAll();

            return self::arrayNotEmpty($return);
        }
        else
        {
            return false;
        }
    }

    public static function describeTable($tblName)
    {
        $newArray = array() ;

        if(self::checkTableExist($tblName))
        {

            $sql = "
               DESCRIBE `".$tblName."`;
            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $describeData = $conn->queryAll();

            for($p = 0 ; $p < sizeof($describeData); $p++ )
            {
                if((strpos($describeData[$p]['Extra'],"auto_increment") === false) && ( $describeData[$p]['Field'] != "updated_at" && $describeData[$p]['Field'] != "created_at" ))
                {
                    $newArray[$describeData[$p]['Field']] = ['type'=>'input', 'value'=>''] ;
                }
                else if( ( $describeData[$p]['Field'] == "updated_at" || $describeData[$p]['Field'] == "created_at" ))
                {
                    $time = strtotime(date('Y-m-d H:i:s'));
                    $newArray[$describeData[$p]['Field']] = ['type'=>'hidden', 'value'=>$time ] ;
                }
            }

            $sql = "

                SELECT
                  TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM
                  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                  TABLE_NAME = '".$tblName."' AND ( REFERENCED_TABLE_NAME IS NOT NULL || REFERENCED_TABLE_NAME <> '' )

            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $describeData = $conn->queryAll();

            for($p = 0 ; $p < sizeof($describeData); $p++ )
            {
                if( AppBasic::arrayKeyExist( $describeData[$p]['COLUMN_NAME'] ,  $newArray))
                {
                    $options = self::getOptionsList($describeData[$p]);
                    $newArray[$describeData[$p]['COLUMN_NAME']] = ['type'=>'select', 'options'=> $options ] ;
                }
            }


            //AppBasic::printRT($describeData , 'describeData');
            //AppBasic::printRT($newArray , 'NEWARRAY SEQUENCE');

            return $newArray;
        }
        else
        {
             trigger_error("Described Table not exist in Database.");
        }

    }

    public static function describeTableView($tblName)
    {
        $newArray = array() ;

        if(self::checkTableExist($tblName))
        {

            $sql = "
               DESCRIBE `".$tblName."`;
            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $describeData = $conn->queryAll();

            for($p = 0 ; $p < sizeof($describeData); $p++ )
            {
                if((strpos($describeData[$p]['Extra'],"auto_increment") === false) && ( $describeData[$p]['Field'] != "updated_at" && $describeData[$p]['Field'] != "created_at" ))
                {
                    $newArray[$describeData[$p]['Field']] = ['type'=>'input', 'value'=>''] ;
                }
                else if( ( $describeData[$p]['Field'] == "updated_at" || $describeData[$p]['Field'] == "created_at" ))
                {
                    $time = strtotime(date('Y-m-d H:i:s'));
                    $newArray[$describeData[$p]['Field']] = ['type'=>'hidden', 'value'=>$time ] ;
                }
            }

            $sql = "

                SELECT
                  TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM
                  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                  TABLE_NAME = '".$tblName."' AND ( REFERENCED_TABLE_NAME IS NOT NULL || REFERENCED_TABLE_NAME <> '' )

            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $describeData = $conn->queryAll();

            for($p = 0 ; $p < sizeof($describeData); $p++ )
            {
                if( AppBasic::arrayKeyExist( $describeData[$p]['COLUMN_NAME'] ,  $newArray))
                {
                    $options = self::getOptionsList($describeData[$p]);
                    $newArray[$describeData[$p]['COLUMN_NAME']] = ['type'=>'select', 'options'=> $options ] ;
                }
            }


            //AppBasic::printRT($describeData , 'describeData');
            //AppBasic::printRT($newArray , 'NEWARRAY SEQUENCE');


            return $newArray;

        }
        else
        {
            trigger_error("Described Table not exist in Database.");
        }

    }

    public static function getOptionArray($TableName , $option, $value )
    {
        if(self::stringNotNull($TableName) && self::stringNotNull($value) && self::stringNotNull($option))
        {
             $sql = ' SELECT '.$option.' as optionv, '.$value.' as value FROM '.$TableName.' ' ;
             $data = \Yii::$app->db->createCommand($sql)->queryAll();

             return ArrayHelper::map($data , 'optionv', 'value');
        }
        else
        {
             return [] ;
        }
    }

    public static function getOptionsList($array)
    {
        if(!empty($array))
        {
            $TableName = AppBasic::arrayKeyExist('REFERENCED_TABLE_NAME', $array , 1);
            $ColumnName = AppBasic::arrayKeyExist('REFERENCED_COLUMN_NAME', $array , 1);

            if(self::checkTableExist($TableName))
            {
                if(self::checkColumnExistInTable($ColumnName, $TableName))
                {
                    if(self::checkColumnExistInTable("name", $TableName))
                    {
                         return self::getOptionArray($TableName ,$ColumnName, "name" );
                    }
                    else if(self::checkColumnExistInTable("title", $TableName))
                    {
                        return self::getOptionArray($TableName ,$ColumnName, "title" );
                    }
                    else if(self::checkColumnExistInTable("first_name", $TableName))
                    {
                        return self::getOptionArray($TableName ,$ColumnName, "first_name" );
                    }
                    else
                    {
                        return self::getOptionArray($TableName, $ColumnName, " CONCAT( '".$TableName." - ' , ".$ColumnName." ) " );
                    }
                }
            }




        }
    }

    public static function findSequence($model)
    {

        if($model != null)
        {
            $tableName = $model::tableName();
            $sequence = self::describeTable($tableName, $model);



            //AppBasic::printRT($sequence , 'SEQUENCE' ) ;
            return $sequence ;



        }

        exit;
    }

    public static function findColumnList($xmlin, $execute_mode, $model_name, $reportico_session_name)
    {

        $model = self::returnModel($model_name);


        //self::printR(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS , 5));
        //exit;

        $tblName = $model::tableName();

        if(self::checkTableExist($tblName))
        {

            $sql = "
               DESCRIBE `".$tblName."`;
            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $describeData = $conn->queryAll();


            $sql = "

                SELECT
                  TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                FROM
                  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                  TABLE_NAME = '".$tblName."' AND ( REFERENCED_TABLE_NAME IS NOT NULL || REFERENCED_TABLE_NAME <> '' )

            " ;

            $conn = \Yii::$app->db->createCommand($sql);
            $foreignKeyData = $conn->queryAll();


            $columnList = [] ;
            for($p = 0 ; $p < sizeof($describeData); $p++ )
            {
                $field = $describeData[$p]['Field'];
                if((strpos($describeData[$p]['Extra'],"auto_increment") !== false) && ($describeData[$p]['Key'] == "PRI"))
                {
                    $columnList[] = [
                        'attribute'=>$field,
                        'format'=>'html',
                        'label'=>'Update',
                        'value'=>function ($model, $key, $index, $column) use ($xmlin, $execute_mode, $model_name, $reportico_session_name, $field ) {

                            $xmlin = str_replace("ADMIN_","FORM_",$xmlin);
                            $id= $model[$field];

                            $url = \Yii::$app->urlManager->createUrl('reportico/reportico/ajax')."?model_name=".$model_name."&execute_mode=PREPARE_MOSLAKE&xmlin=".$xmlin."&reportico_session_name=NS_&coreReportId=&id=".$id;
                            return Html::a($id, $url , ['class' => 'btn-link swMenuItemLink']);
                        }
                    ] ;
                }
                else if( ( $describeData[$p]['Field'] == "updated_at" || $describeData[$p]['Field'] == "created_at" ))
                {

                    $columnList[] = [
                        'attribute'=> $field ,
                        'format'=>["date", "php:m-d-Y h:i A"],
                    ] ;

                }
                else if($describeData[$p]['Key'] == 'MUL' )
                {
                    for($q=0; $q<sizeof($foreignKeyData); $q++)
                    {
                        $fieldAnTable = $foreignKeyData[$q]['COLUMN_NAME'];
                        $TableName = $foreignKeyData[$q]['REFERENCED_TABLE_NAME'];
                        if($fieldAnTable == $field)
                        {

                            //self::printRT($fieldAnTable , 'fieldAnTable' );
                            $nField = self::makeModeleAttributeNameForRelation($field);
                            if(self::checkColumnExistInTable("name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.name'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("title", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.title'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("database_name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.database_name'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("client_name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.client_name'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("display_name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.display_name'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("encoding_name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.encoding_name'] ;
                                break;
                            }
                            else if(self::checkColumnExistInTable("first_name", $TableName))
                            {
                                $columnList[] = ['attribute'=>$field,  'value' => $nField.'.first_name' ] ;
                                break;
                            }
                            else
                            {
                                $columnList[] = $field ;
                                break;
                            }
                        }


                    }
                }
                else
                {
                    $columnList[] = $field ;
                }

            }


            //$columnList[] = ['attribute'=>'link_dependecy_id', 'value'=>'linkDependency.name'];


            /*

            $columnList =   [
                [
                    'attribute'=>'create_module_id',
                    'format'=>'html',
                    'label'=>'Update',
                    'value'=>function ($model, $key, $index, $column) use ($xmlin, $execute_mode, $model_name, $reportico_session_name) {

                        $xmlin = str_replace("ADMIN_","FORM_",$xmlin);
                        $id= $model['create_module_id'];

                        $url = Yii::$app->urlManager->createUrl('reportico/reportico/ajax')."?model_name=".$model_name."&execute_mode=PREPARE_MOSLAKE&xmlin=".$xmlin."&reportico_session_name=NS_&coreReportId=&id=".$id;
                        return Html::a($id, $url , ['class' => 'btn-link swMenuItemLink']);
                    }
                ],
                'name',
                'report',
                'title',
                'language',
                'model_name',
                'link_dependency',
                'show_link',
                'admin_link',
                [
                    'attribute'=>'created_at',
                    'format'=>["date", "php:m-d-Y h:i A"],
                ],

                [
                    'attribute'=>'updated_at',
                    'format'=>["date", "php:m-d-Y h:i A"],
                ],
            ] ;

            */

            return $columnList ;
        }
    }


    public static function getModuleTitle($report , $name)
    {
       $sql = 'SELECT '.$name.' FROM `create_module` WHERE `create_module`.`report` =:report ';
       $data = \Yii::$app->db->createCommand($sql , [':report'=>$report] )->queryOne();

       return  self::arrayKeyExist( $name , $data ) ;
    }

    public static function makeModeleName($tblName)
    {
        $pieces = explode("_", $tblName);

        $modelName =  "" ;
        for($i = 0 ; $i < sizeof($pieces); $i++)
        {
            $modelName .= ucfirst($pieces[$i]);
        }

        return $modelName ;
    }

    public static function makeUserAttributeName($tblName)
    {
        $pieces = explode("_", $tblName);

        $modelName =  "" ;
        for($i = 0 ; $i < sizeof($pieces); $i++)
        {
            $modelName .= ucfirst($pieces[$i])." ";
        }

        return $modelName ;
    }

    public static function makeModeleAttributeNameForRelation($attrName)
    {
        $pieces = explode("_", $attrName);

        $attrNameNew =  "" ;
        for($i = 0 ; $i < sizeof($pieces); $i++)
        {
            if($i == 0)
            {
                $attrNameNew .= $pieces[$i];
            }
            else
            {
                $attrNameNew .= ucfirst($pieces[$i]);
            }
        }

        return $attrNameNew ;
    }

    public static function checkAccess()
    {
        $userId = \Yii::$app->user->getId();

        $sql = "

                SELECT
                   application_roles.name
                FROM
                   application_roles
                   INNER JOIN user ON user.application_role_id = application_roles.application_role_id
                WHERE
                   user.user_id=:user_id

            " ;

        $conn = \Yii::$app->db->createCommand($sql);
        $conn->bindParam(':user_id',$userId);
        $roleData = $conn->queryOne();

        $role = false ;
        if(!empty($roleData))
        {
            $role = $roleData['name'];
        }
        
        $url = \Yii::$app->getUrlManager()->createUrl('site/login');
        $finalMessage = "Please Login to continue.";


        if(in_array($role , [User::SUPER_ADMIN,User::SUB_ADMIN,User::CLIENT_ADMIN,User::CLIENT] ))
        {
            if (\Yii::$app->user->isGuest)
            {
                \Yii::$app->session->setFlash('info',$finalMessage);
                $controller = new SiteController();
                $controller->goLogin();
                //throw phone HttpException(403,$finalMessage);
            }
            else
            {
                if( in_array($role, [User::CLIENT_ADMIN,User::CLIENT]) )
                {
                    if(\Yii::$app->request->isAjax)
                    {
                    }
                    elseif(isset($_REQUEST['MVP_REQUEST']) || FrontEndHelper::$retrive_data)
                    {
                    }
                    else
                    {
                        throw new HttpException(431,"You do not have rights to view this page.");
                    }
                }
            }
        }
        else
        {
            throw new HttpException(431,$finalMessage);
        }
    }

    public static function checkCoreReportExist($coreReportId)
    {
        $sql = "
        SELECT  core_report_id as c , report_name FROM core_reports WHERE core_report_id = :core_report_id ;
        " ;

        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(':core_report_id' , $coreReportId);
        $data =  $command->queryOne();

        if(empty($data))
        {
            return  0 ;
        }
        else if($data['c'] == 0)
        {
            return  0 ;
        }

        return  1 ;
    }

    public static function getCriteriaForCoreReport($coreReportId)
    {
        $sql = "
        SELECT * FROM criteria WHERE core_report_id = :core_report_id ;
        " ;

        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(':core_report_id' , $coreReportId);
        $data =  $command->queryAll();

        if(empty($data))
        {
            return  false ;
        }

        return  $data ;
    }

    public static function getAssignmentsForCoreReport($coreReportId)
    {
        $sql = "
        SELECT * FROM assignments WHERE core_report_id = :core_report_id ;
        " ;

        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(':core_report_id' , $coreReportId);
        $data =  $command->queryAll();

        if(empty($data))
        {
            return  false ;
        }

        return  $data ;
    }

    public static function getPageHeadersForCoreReport($coreReportId , $column)
    {
        $sql = "
        SELECT * FROM page_header_or_footer WHERE core_report_id = :core_report_id AND ( ".$column." IS NULL || ".$column." = ''  ) ;
        " ;

        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(':core_report_id' , $coreReportId);
        $data =  $command->queryAll();

        if(empty($data))
        {
            return  false ;
        }

        return  $data ;
    }

    public static function loadReportFromDatabase($coreReportId)
    {
        $q = new reportico();
        $reader = new reportico_xml_reader($q, false, false);
        $reader->sql2query(null , $coreReportId, 1);

        return $q;
    }


    /*
** Gets the value of a reportico session_param
** using current session namespace
*/
    public static function get_reportico_session_param($param)
    {
        global $g_session_namespace_key;
        if ( isset ( $_SESSION[$g_session_namespace_key][$param] ) )
        {
            return $_SESSION[$g_session_namespace_key][$param];
        }
        else
            return false;
    }

    public static function getArrayDone($ts1, $t1, $tc1, $tempArray0, $nextTable , $field, $value)
    {
        if($ts1)
        {
            if(isset($tempArray0[$t1]))
            {
                if(!isset($tempArray0[$t1][$tc1]))
                {
                    $tempArray0[$t1][$tc1] = [] ;
                }
            }
            else
            {
                $tempArray0[$t1][$tc1] = [] ;
            }

            if(!AppBasic::stringNotNull($nextTable))
            {
                $tempArray0[$t1][$tc1][$field] = $value ;
            }
        }
        else
        {
            if(!isset($tempArray0[$t1]))
            {
                $tempArray0[$t1] = [] ;
            }

            if(!AppBasic::stringNotNull($nextTable))
            {
                $tempArray0[$t1][$field] = $value ;
            }

        }

        return $tempArray0;
    }


    public static function getPhpJsonForReport($id, $record_data = [])
    {
       $sql = '
       
       SELECT vjd.core_report_id,
       vjd.field_id,
       vjd.value,
       REPLACE(vfc.name, "v_", "") AS fieldName,
       REPLACE(vtc.name, "visualization_", "") AS t1,
       REPLACE(vtc1.name, "visualization_", "") AS t2,
       REPLACE(vtc2.name, "visualization_", "") AS t3,
       REPLACE(vtc3.name, "visualization_", "") AS t4,
       REPLACE(vtc4.name, "visualization_", "") AS t5,
       REPLACE(vtc5.name, "visualization_", "") AS t6,
       vtc.is_sequential_array AS ts1,
       vtc1.is_sequential_array AS ts2,
       vtc2.is_sequential_array AS ts3,
       vtc3.is_sequential_array AS ts4,
       vtc4.is_sequential_array AS ts5,
       vtc5.is_sequential_array AS ts6,
       vjd.t1_count AS tc1,
       vjd.t2_count AS tc2,
       vjd.t3_count AS tc3,
       vjd.t4_count AS tc4,
       vjd.t5_count AS tc5,
       vjd.t6_count AS tc6
  FROM visualization_json_data vjd
       LEFT JOIN visualization_field_config vfc
          ON vjd.field_id = vfc.field_id
       LEFT JOIN visualization_table_config vtc
          ON vtc.table_id = vjd.t1_table
       LEFT JOIN visualization_table_config vtc1
          ON vtc1.table_id = vjd.t2_table
       LEFT JOIN visualization_table_config vtc2
          ON vtc2.table_id = vjd.t3_table
       LEFT JOIN visualization_table_config vtc3
          ON vtc3.table_id = vjd.t4_table
       LEFT JOIN visualization_table_config vtc4
          ON vtc4.table_id = vjd.t5_table
       LEFT JOIN visualization_table_config vtc5
          ON vtc5.table_id = vjd.t6_table
 WHERE vjd.core_report_id = :core_report_id
       -- GROUP BY visualization_json_data_id
       
       ' ;

       $command = \Yii::$app->db->createCommand($sql);
       $command->bindParam(':core_report_id', $id);
       $data =  $command->queryAll();

        $catValue = "" ;
        $newJsonArray = array();
        $seriesArray = [] ;
        $categoryArray = [] ;

        for($i = 0 ; $i < sizeof($data); $i++ )
        {
            $value = $data[$i]['value'];
            $fieldName = $data[$i]['fieldName'];
            $t1 = $data[$i]['t1'];
            $t2 = $data[$i]['t2'];
            $t3 = $data[$i]['t3'];
            $t4 = $data[$i]['t4'];
            $t5 = $data[$i]['t5'];
            $t6 = $data[$i]['t6'];
            $ts1 = $data[$i]['ts1'];
            $ts2 = $data[$i]['ts2'];
            $ts3 = $data[$i]['ts3'];
            $ts4 = $data[$i]['ts4'];
            $ts5 = $data[$i]['ts5'];
            $ts6 = $data[$i]['ts6'];
            $tc1 = $data[$i]['tc1'];
            $tc2 = $data[$i]['tc2'];
            $tc3 = $data[$i]['tc3'];
            $tc4 = $data[$i]['tc4'];
            $tc5 = $data[$i]['tc5'];
            $tc6 = $data[$i]['tc6'];


            //AppBasic::printRT($newJsonArray, 'p');
            //AppBasic::printRT($data[$i], 'q');

            if(AppBasic::stringNotNull($t1))
            {
                $newJsonArray = self::getArrayDone($ts1, $t1, $tc1, $newJsonArray, $t2, $fieldName, $value);
            }

            if(AppBasic::stringNotNull($t2))
            {
                if($ts1)
                {
                    $newJsonArray[$t1][$tc1] = self::getArrayDone($ts2, $t2, $tc2, $newJsonArray[$t1][$tc1], $t3, $fieldName, $value);
                }
                else
                {
                    $newJsonArray[$t1] = self::getArrayDone($ts2, $t2, $tc2, $newJsonArray[$t1], $t3, $fieldName, $value);
                }

            }

            if(AppBasic::stringNotNull($t3))
            {
                if($ts2)
                {
                    if($ts1)
                    {
                        $newJsonArray[$t1][$tc1][$t2][$tc2] = self::getArrayDone($ts3, $t3, $tc3, $newJsonArray[$t1][$tc1][$t2][$tc2], $t4, $fieldName, $value);
                    }
                    else
                    {
                        $newJsonArray[$t1][$t2][$tc2] = self::getArrayDone($ts3, $t3, $tc3, $newJsonArray[$t1][$t2][$tc2], $t4, $fieldName, $value);
                    }
                }
                else
                {
                    if($ts1)
                    {
                        $newJsonArray[$t1][$tc1][$t2] = self::getArrayDone($ts3, $t3, $tc3, $newJsonArray[$t1][$tc1][$t2] , $t4, $fieldName, $value);
                    }
                    else
                    {
                        $newJsonArray[$t1][$t2] = self::getArrayDone($ts3, $t3, $tc3, $newJsonArray[$t1][$t2], $t4, $fieldName, $value);
                    }
                }
            }

            if(AppBasic::stringNotNull($t4))
            {
                if($ts3)
                {
                    if($ts2)
                    {
                        if($ts1)
                        {
                            $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3], $t5, $fieldName, $value);
                        }
                        else
                        {
                            $newJsonArray[$t1][$t2][$tc2][$t3][$tc3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3] , $t5, $fieldName, $value);
                        }
                    }
                    else
                    {
                        if($ts1)
                        {
                            $newJsonArray[$t1][$tc1][$t2][$t3][$tc3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3] , $t5, $fieldName, $value);
                        }
                        else
                        {
                            $newJsonArray[$t1][$t2][$t3][$tc3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$t2][$t3][$tc3] , $t5, $fieldName, $value);
                        }
                    }
                }
                else
                {
                    if($ts2)
                    {
                        if($ts1)
                        {
                            $newJsonArray[$t1][$tc1][$t2][$tc2][$t3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3] , $t5, $fieldName, $value);
                        }
                        else
                        {
                            $newJsonArray[$t1][$t2][$tc2][$t3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$t2][$tc2][$t3] , $t5, $fieldName, $value);
                        }
                    }
                    else
                    {
                        if($ts1)
                        {
                            $newJsonArray[$t1][$tc1][$t2][$t3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$tc1][$t2][$t3] , $t5, $fieldName, $value);
                        }
                        else
                        {
                            $newJsonArray[$t1][$t2][$t3] = self::getArrayDone($ts4, $t4, $tc4, $newJsonArray[$t1][$t2][$t3], $t5, $fieldName, $value);
                        }
                    }
                }
            }

            if(AppBasic::stringNotNull($t5))
            {
                if($ts4)
                {
                    if($ts3)
                    {
                        if($ts2)
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4]  = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4]   , $t6, $fieldName, $value);
                            }
                        }
                        else
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4]   , $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5,$newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                        }
                    }
                    else
                    {
                        if($ts2)
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                        }
                        else
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$t3][$t4][$tc4]   = self::getArrayDone($ts5, $t5, $tc5,$newJsonArray[$t1][$t2][$t3][$t4][$tc4], $t6, $fieldName, $value);
                            }
                        }
                    }
                }
                else
                {
                    if($ts3)
                    {
                        if($ts2)
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4], $t6, $fieldName, $value);
                            }
                        }
                        else
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$t3][$tc3][$t4] = self::getArrayDone($ts5, $t5, $tc5,$newJsonArray[$t1][$t2][$t3][$tc3][$t4], $t6, $fieldName, $value);
                            }
                        }
                    }
                    else
                    {
                        if($ts2)
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$tc2][$t3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$t2][$tc2][$t3][$t4], $t6, $fieldName, $value);
                            }
                        }
                        else
                        {
                            if($ts1)
                            {
                                $newJsonArray[$t1][$tc1][$t2][$t3][$t4] = self::getArrayDone($ts5, $t5, $tc5, $newJsonArray[$t1][$tc1][$t2][$t3][$t4], $t6, $fieldName, $value);
                            }
                            else
                            {
                                $newJsonArray[$t1][$t2][$t3][$t4] = self::getArrayDone($ts5, $t5, $tc5,$newJsonArray[$t1][$t2][$t3][$t4], $t6, $fieldName, $value);
                            }
                        }
                    }
                }
            }

            if(AppBasic::stringNotNull($t6))
            {
                $ts7 = "" ;
                if($ts5)
                {
                    if($ts4)
                    {
                        if($ts3)
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5][$tc5]  = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                        }
                        else
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$t4][$tc4][$t5][$tc5]   = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$t4][$tc4][$t5][$tc5]   , $t7, $fieldName, $value);
                                }
                            }
                        }
                    }
                    else
                    {
                        if($ts3)
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$tc3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$tc3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                        }
                        else
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$t4][$t5][$tc5] = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$t4][$t5][$tc5], $t7, $fieldName, $value);
                                }
                            }
                        }
                    }
                }
                else
                {
                    if($ts4)
                    {
                        if($ts3)
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5]  = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$tc3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                            }
                        }
                        else
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$tc4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$t4][$tc4][$t5]   = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$t4][$tc4][$t5]   , $t7, $fieldName, $value);
                                }
                            }
                        }
                    }
                    else
                    {
                        if($ts3)
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$tc3][$t4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$tc3][$t4][$t5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$tc3][$t4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$tc3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$tc3][$t4][$t5], $t7, $fieldName, $value);
                                }
                            }
                        }
                        else
                        {
                            if($ts2)
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$tc2][$t3][$t4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$t2][$tc2][$t3][$t4][$t5], $t7, $fieldName, $value);
                                }
                            }
                            else
                            {
                                if($ts1)
                                {
                                    $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6, $newJsonArray[$t1][$tc1][$t2][$t3][$t4][$t5], $t7, $fieldName, $value);
                                }
                                else
                                {
                                    $newJsonArray[$t1][$t2][$t3][$t4][$t5] = self::getArrayDone($ts6, $t6, $tc6,$newJsonArray[$t1][$t2][$t3][$t4][$t5], $t7, $fieldName, $value);
                                }
                            }
                        }
                    }

                }
            }

        }


        //AppBasic::printRT($newJsonArray);
        //exit;
        
        $series = [] ;
        $series1 = [] ;
        $seriesA = self::arrayKeyExist( 'series' , $newJsonArray, 0) ? $newJsonArray['series']  : [] ;
        $seriesArray = self::arrayKeyExist( 0 , $seriesA, 0) ? $seriesA[0] : [] ;
        $valName = self::arrayKeyExist( "name" , $seriesArray , 1) ;
        $valNameSeparator = self::arrayKeyExist( "name_seperator" , $seriesArray , 1) ;
        $dataToShow = self::arrayKeyExist( "dataToShow" , $seriesArray , 1) ;
        $dataA = self::arrayKeyExist( "data" , $seriesArray , 0)  ;

        $plotOptions = self::arrayKeyExist( "plotOptions" , $newJsonArray , 1 , 1) ;
        $chart = self::arrayKeyExist( "chart" , $newJsonArray , 1) ;
        $chartType = self::arrayKeyExist( "type" , $chart , 1) ;

        if(!empty($plotOptions) && self::stringNotNull($chartType))
        {
            $plotOptionsNew[$chartType] = $plotOptions ;
            $newJsonArray['plotOptions'] = $plotOptionsNew ;
        }


        //AppBasic::printRT($record_data);
        //self::printRT($dataToShow , 'dataToShow');
        $xAxis =  AppBasic::arrayKeyExist('xAxis', $newJsonArray, 1) ;
        $cat = AppBasic::arrayKeyExist('categories', $xAxis, 1);
        $catSeperator = AppBasic::arrayKeyExist('categories_seperator', $xAxis, 1);

        if(self::stringNotNull($cat) && self::stringNotNull($catSeperator))
        {
            $catLastValueArray = [] ;
            $catNew = [] ;
            for($k = 0 ; $k < sizeof($record_data); $k++)
            {
                $record =  $record_data[$k] ;
                $catValue =  $record_data[$k][$cat] ;
                $catSeperatorValue =  $record_data[$k][$catSeperator] ;

                if(!in_array($catSeperatorValue, $catLastValueArray))
                {
                        $catNew[] = $catValue;
                }

                $catLastValueArray[] = $catSeperatorValue ;
            }

            $xAxis['categories'] = $catNew ;
            unset($xAxis['categories_seperator']);
            $newJsonArray['xAxis'] = $xAxis;

        }
        else
        {
        }


        $columns = FrontEndHelper::findQueryColumns($id);
        $columnsArray = [] ;
        for($i = 0 ; $i < sizeof($columns); $i++)
        {
            $columnsArray[] = $columns[$i]['Name'] ;
        }

        if(!$dataA)
        {
            if(!in_array($valName , $columnsArray))
            {

                for($i = 0 ; $i < sizeof($record_data); $i++)
                {
                    $record =  $record_data[$i] ;
                    $series1[] =  self::arrayKeyExist( $dataToShow , $record , 1) ;
                }

                $series[0]['name'] = $valName;
                $series[0]['data'] = $series1;

                $newJsonArray['series'] = $series ;
            }
            else
            {

                $lastNameArray = [] ;
                $dataArray = [] ;
                $p = 0;
                for($i = 0 ; $i < sizeof($record_data); $i++)
                {
                    $record =  $record_data[$i] ;
                    $dataToShowValue =  self::arrayKeyExist( $dataToShow , $record , 1) ;
                    $valNameValue =  self::arrayKeyExist( $valName , $record , 1) ;
                    $valNameSeparatorValue = self::arrayKeyExist($valNameSeparator, $record , 1);

                    if(!in_array( $valNameSeparatorValue , $lastNameArray))
                    {
                        $p = sizeof($series1);
                        $dataArray = [] ;
                    }

                    $dataArray[] = $dataToShowValue;
                    $series1[$p]['name']  = $valNameValue ;
                    $series1[$p]['data']  = $dataArray ;

                    $lastNameArray[] = $valNameSeparatorValue ;
                }

                $newJsonArray['series'] = $series1 ;

            }
        }
        else
        {

            $dataA = self::arrayKeyExist( "data" , $seriesArray , 1)  ;
            $dataA = self::arrayKeyExist( 0 , $dataA , 1)  ;
            $nameData = self::arrayKeyExist( "name" , $dataA , 1) ;
            $yData = self::arrayKeyExist( "y" , $dataA , 1) ;

            $series1 = [] ;
            if(self::stringNotNull($nameData) && self::stringNotNull($yData))
            {
                if(in_array($nameData , $columnsArray) && in_array($yData , $columnsArray))
                {
                    for($i = 0 ; $i < sizeof($record_data); $i++)
                    {
                        $record =  $record_data[$i] ;

                        $p = sizeof($series1);
                        $series1[$p]['name'] =  self::arrayKeyExist( $nameData , $record , 1) ;
                        $series1[$p]['y'] =  self::arrayKeyExist( $yData , $record , 1) ;

                        $sliced = self::arrayKeyExist( "sliced" , $record , 1)  ;
                        $selected = self::arrayKeyExist( "selected" , $record , 1)  ;

                        if($sliced)
                        {
                            $series1[$p]['sliced'] =  1;
                        }

                        if($selected)
                        {
                            $series1[$p]['selected'] =  1;
                        }

                    }
                }
            }

            $seriesA[0]['data'] = $series1 ;
            $newJsonArray['series'] = $seriesA;

        }

        
        //AppBasic::printR($newJsonArray);
        //exit;
         return json_encode($newJsonArray);
}


}