<?php
class DisjointModel extends Model_Table {
    public $type=null;
    function init(){
        parent::init();

        $this->addCondition('type',$this->type);
        $this->addExpression('is_editable')->set(array($this,'isEditable'));
    }
    function isEditable($m=null){
        if(!$this->hasElement('user_id'))return 0;
        if($m){
            if($this->api->me['is_admin'])return 1;

            return $this->_dsql()->expr('if([user_id]=[me],1,0)',
                array(
                    'user_id'=>$this->getElement('user_id'),
                    'me'=>$this->api->me->id
                )
            );
        }else{
            return $this['is_editable'];
        }
    }
    function loadSubType($id=null){
        if(!$id)$id=$this->id;
        return $this
            ->add($this->api->normalizeClassName($this->type,'Model'))
            ->load($id);
    }
    function initTimestamps(){
        $this->addField('created_dts')->defaultValue(date('Y-m-d H:i:s'))->system(true);
    }
}
