<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property integer $id
 * @property integer $webpage_id
 * @property string $tag
 * @property string $head_body
 * @property string $html
 */
class Tag extends \yii\db\ActiveRecord
{    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['webpage_id', 'name', 'html'], 'required'],
            [['webpage_id'], 'integer'],
            [['html'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'webpage_id' => Yii::t('app', 'Webpage ID'),
            'name' => Yii::t('app', 'Name'),
            'html' => Yii::t('app', 'Html'),
        ];
    }

    /**
     * @inheritdoc
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(get_called_class());
    }
    
    public function getTags($html, $tags = [], $html_begin_pos = 0){ 
        $tag = $this->_getTag($html, $html_begin_pos);
        
        // if there is no more tags
        if(false === $tag['begin_start_pos']){
            return $tags;
        }
        
        // if it is no tag
        if(false === $tag['name']){
            return $this->getTags($html, $tags, $html_begin_pos+1);
        }
        
        // find tag end position
        $tag_end_start_pos_found = false;
        $begin_end_pos = $tag['begin_end_pos'];
        $next_tag = [];
        
        while(!$tag_end_start_pos_found){
            // get next tag end position
            list($next_tag['begin_start_pos'], $next_tag['begin_end_pos']) = $this->getBeginPos($html, $begin_end_pos, $tag['name']);
            
            // if the next tag begin pos is between the tag begin end pos and before the tag end end pos 
            if($next_tag['begin_start_pos'] >= $tag['begin_end_pos'] and $next_tag['begin_start_pos'] < $tag['end_start_pos']){
                
                list($next_tag['end_start_pos'], $next_tag['end_end_pos']) = $this->getEndPos($html, $tag['name'], $tag['end_end_pos']);
                $begin_end_pos = $next_tag['begin_end_pos'];
                
                $tag['end_start_pos'] = $next_tag['end_start_pos'];     
                $tag['end_end_pos'] = $next_tag['end_end_pos'];
                
            }else {
                $tag_end_start_pos_found = true;
            }            
        }
                
        // get the html 
        $tag['html'] = $this->getHtml($html, $tag['begin_start_pos'], $tag['end_end_pos']);
        
        // get the text 
        $tag['text'] = $this->getText($html, $tag['name'], $tag['begin_end_pos'], $tag['end_start_pos']);
        
        // get attributes
        $tag['attr'] = $this->getAttrs($html, $tag['begin_start_pos'], $tag['begin_end_pos']);
        
        $tags[$tag['begin_start_pos']] = $tag;
        
        return $this->getTags($html, $tags, $tag['begin_end_pos']);
    }
    
    public function _getTag($html, $html_begin_pos, $name = ''){
        list($begin_tag_start_pos, $begin_tag_end_pos) = $this->getBeginPos($html, $html_begin_pos, $name);
        if(empty($name)){
            $name = $this->getName($html, $begin_tag_start_pos, $begin_tag_end_pos);
        }
        list($end_tag_start_pos, $end_tag_end_pos) = $this->getEndPos($html, $name, $begin_tag_start_pos+1);
        
        return ['name' => $name, 'begin_start_pos' => $begin_tag_start_pos, 'begin_end_pos' => $begin_tag_end_pos, 'end_start_pos' => $end_tag_start_pos, 'end_end_pos' => $end_tag_end_pos];
    }
    
    public function getBeginPos($html, $html_begin_pos, $name = ''){
        if(empty($name)){
            $begin_tag_start_pos = strpos($html, '<', $html_begin_pos);
        }else {
            $begin_tag_start_pos = strpos($html, '<' . $name, $html_begin_pos);
        }
        $begin_tag_end_pos = strpos($html, '>', $begin_tag_start_pos+1);
                
        return [$begin_tag_start_pos, $begin_tag_end_pos+1];
    }
    
    public function getEndPos($html, $name, $begin_tag_end_pos){
        $tags_no_end = ['meta', 'link', 'img', 'br'];
        
        if(in_array($name, $tags_no_end)){
            $end_tag_start_pos = $begin_tag_end_pos;
            $end_tag_end_pos = strpos($html, '>', $begin_tag_end_pos);
        }else {
            $end_tag_start_pos = strpos($html, '</' . $name, $begin_tag_end_pos);
            $end_tag_end_pos = strpos($html, '>', $end_tag_start_pos+1);
        }
        
        return [$end_tag_start_pos, $end_tag_end_pos+1];
    }
        
    public function getName($html, $begin_tag_start_pos, $begin_tag_end_pos){
        // if character  after < is not a character it is not a tag
        $char = substr($html, $begin_tag_start_pos+1, 1);
        if(!ctype_alpha($char)){
            return false;
        }

        // tag it self
        $tag_end = 0;
        // white space before begin tag end pos
        $white_space = strpos($html, ' ', $begin_tag_start_pos);
        if(false !== $white_space){
            if($white_space < $begin_tag_end_pos){
                $tag_end = $white_space;
            }else {
                $tag_end = $begin_tag_end_pos-1;
            }
        }else {
            $tag_end = $begin_tag_end_pos-1;
        }

        $tag = substr($html, $begin_tag_start_pos+1, $tag_end - ($begin_tag_start_pos + 1));
        
        return $tag;
    }
    
    public function getHtml($html, $begin_pos, $end_pos){
        return substr($html, $begin_pos, $end_pos - $begin_pos); 
    }
    
    public function getText($html, $name, $begin_end_pos, $end_start_pos){
        $tags_no_text = ['meta', 'link', 'img', 'br'];
        
        if(in_array($name, $tags_no_text)){
            return '';
        }
        
        $inner_html = substr($html, $begin_end_pos, $end_start_pos - $begin_end_pos);
        echo('$inner_html') . PHP_EOL;
        var_dump($inner_html);
        /*$inner_tag = $this->_getTag($inner_html, 0);
        echo('$inner_tag') . PHP_EOL;
        var_dump($inner_tag);*/
        
        list($inner_tag['begin_start_pos'], $inner_tag['begin_end_pos']) = $this->getBeginPos($inner_html, 0);
        echo('$inner_tag') . PHP_EOL;
        var_dump($inner_tag);   
        
        // if there is no inner tag
        if(false === $inner_tag['begin_start_pos']){
            return trim($inner_html);
        }

        // if it is no inner tag
        if(false === $inner_tag['name']){
            return trim($inner_html);
        }

        $inner_tag['end_edn_pos'] = strrpos
        
        $text = substr_replace($inner_html, '', $inner_tag['begin_start_pos'], ($inner_tag['end_end_pos'] + 1) - $inner_tag['begin_start_pos']);
        $text = trim($text);
        echo('$text') . PHP_EOL;
        var_dump($text);
        return trim($text);
    }
    
    public function getAttrs($html, $begin_start_pos, $begin_end_pos){
        $attr_html = substr($html, $begin_start_pos, $begin_end_pos - $begin_start_pos);
        
        $all_attrs_found = false;
        $attr_begin_pos = 0;
        $attrs = [];
        
        while(!$all_attrs_found){
            $attr = $this->getAttr($attr_html, $attr_begin_pos);
            if(!empty($attr)){
                //$attrs[$attr['start_pos']] = $attr;
                $attrs[$attr['name']] = ['name' => $attr['name'], 'value' => $attr['value']];
                $attr_begin_pos = $attr['end_pos']+1;

            }else {
                $all_attrs_found = true;
            }
        }
        
        return $attrs;
    }
    
    public function getAttr($attr_html, $attr_begin_pos){
        list($attr_start_pos, $attr_value_start_pos) = $this->getAttrStartPos($attr_html, $attr_begin_pos);
        if(false === $attr_start_pos){
            return [];
        }
        
        $attr_end_pos = $this->getAttrEndPos($attr_html, $attr_value_start_pos);
        $attr_name = $this->getAttrName($attr_html, $attr_start_pos, $attr_value_start_pos);
        $attr_value = $this->getAttrValue($attr_html, $attr_value_start_pos, $attr_end_pos);
        
        return ['start_pos' => $attr_start_pos, 'end_pos' => $attr_end_pos, 'name' => $attr_name, 'value_start_pos' => $attr_value_start_pos, 'value' => $attr_value];
    }
    
    public function getAttrStartPos($attr_html, $attr_begin_pos){
        $attr_value_start_pos = strpos($attr_html, '="', $attr_begin_pos);
        if(false === $attr_value_start_pos){
            return [false, false];
        }
        
        // find empty space backwards from de ="
        $attr_start_pos = strrpos($attr_html, ' ', (-1 * (strlen($attr_html) - $attr_value_start_pos))); // offest must be nagative
        
        return [$attr_start_pos, $attr_value_start_pos];
    }
    
    public function getAttrEndPos($attr_html, $attr_value_start_pos){
        return strpos($attr_html, '"', $attr_value_start_pos+2);               
    }
    
    public function getAttrName($attr_html, $attr_start_pos, $attr_value_start_pos){
        return substr($attr_html, $attr_start_pos+1, ($attr_value_start_pos - ($attr_start_pos + 1)));
    }
    
    public function getAttrValue($attr_html, $attr_value_start_pos, $attr_end_pos){
        return substr($attr_html, $attr_value_start_pos+2, ($attr_end_pos - ($attr_value_start_pos + 2)));
    }
    
    public function getHierarchy($tags){
        ksort($tags);
        foreach($tags as $begin_pos => $tag){
            if(!isset($tags[$begin_pos]['parent'])){
                $tags[$begin_pos]['parent'] = 'none';
            }
            
            foreach($tags as $next_begin_pos => $next_tag){
                if($tag['begin_end_pos'] <= $next_tag['begin_start_pos'] and $tag['end_start_pos'] >= $next_tag['end_end_pos']){
                    $tags[$next_begin_pos]['parent'] = $begin_pos;
                }
            }
        }
        
        return $tags;
    }
}
