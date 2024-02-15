<?php







        

 $formid = $_POST["form_id"];

  if ($formid == 0){
      $NAME = $_POST["wsf_name"];
        $PHONE = $_POST["wsf_phone"];
       $EMAIL = $_POST["wsf_email"];
$TITLE = $_POST["wsf_enquiry"];


}

/*
else if ($formid == 31){
$prod = $arFields["PROPERTY_VALUES"]["PRODUCT_INFO"]["VALUE"]["TEXT"];
$TITLE = 'Консультация по продукту '.$prod;
}
*/
$arFields = array (
    'TITLE' => $TITLE,
    'NAME' =>$NAME,
    'PHONE' => (!empty($PHONE)) ? array(array('VALUE' => $PHONE, 'VALUE_TYPE' => 'WORK')) : array(),
    'EMAIL' => (!empty($EMAIL)) ? array(array('VALUE' => $EMAIL, 'VALUE_TYPE' => 'WORK')) : array(),
    'SOURCE_ID' => 3,
    'COMMENTS' =>$desc,
    //'SOURCE_DESCRIPTION' =>  $prod. "Форма заявки",
    );


if(!empty($PHONE)) {

require_once('src/crest.php');


$arLeadDuplicate = array ();
if(!empty($PHONE)){
    $arResultDuplicate = CRest::call('crm.duplicate.findbycomm',array (
        "entity_type" => "LEAD",
        "type" => "PHONE",
        "values" => array($PHONE)
    ));
    if(!empty($arResultDuplicate['result']['LEAD'])){
        $arLeadDuplicate = array_merge ($arLeadDuplicate,$arResultDuplicate['result']['LEAD']);
    }
}

if(!empty($EMAIL)) {
    $arResultDuplicate = CRest::call('crm.duplicate.findbycomm', array (
        "entity_type" => "LEAD",
        "type" => "EMAIL",
        "values" => array($EMAIL)
    ));
    if(!empty($arResultDuplicate[ 'result' ][ 'LEAD' ])) {
        $arLeadDuplicate = array_merge($arLeadDuplicate, $arResultDuplicate[ 'result' ][ 'LEAD' ]);
    }
}

if(!empty($arLeadDuplicate)){
    $arDuplicateLead = CRest::call('crm.lead.list',array (
        "filter" => array(
            '=ID' => $arLeadDuplicate,
            'STATUS_ID' => 'CONVERTED',
        ),
        'select' => array(
            'ID', 'COMPANY_ID', 'CONTACT_ID'
        )
    ));
    
    if(!empty($arDuplicateLead['result'])){
        $sCompany = reset(array_diff(array_column($arDuplicateLead['result'],'COMPANY_ID','ID'),array('')));
        $sContact = reset(array_diff(array_column($arDuplicateLead['result'],'CONTACT_ID','ID'),array('')));
        if($sCompany > 0)
            $arFields['COMPANY_ID'] = $sCompany;
        if($sContact > 0)
            $arFields['CONTACT_ID'] = $sContact;
    }
}

$resultLead = CRest::call('crm.lead.add',
   array (
        'fields'    =>  $arFields
    )
);
/*
 if(!empty($resultLead ['result'])){
        echo 'ok';
    }elseif(!empty($resultLead ['error_description'])){
        echo json_encode(['message' => 'Lead not added: '.$result['error_description']]);
    }else{
        echo json_encode(['message' => 'Lead not added']);
    }
*/






	

            
  }






?>