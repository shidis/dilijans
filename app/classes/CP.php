<?

final class App_CP extends CP
{

    function isAllow($name)
    {
        $this->lastName=$name;

        if($name=='orders.del')
            if(in_array(CMS_LEVEL_ACCESS,array(1,2,3))) return true; else return false;


        if($name=='callLog')
            if (CMS_LEVEL_ACCESS == 1) return true; else return false;



        if(CMS_LEVEL_ACCESS<50) return true;


        if(CMS_LEVEL_ACCESS==51 || CMS_LEVEL_ACCESS==53)
            if(in_array($name,array('models.imageEdit', 'models.edit'))) return true;

        return false;
    }

    /* CMS_LEVEL_ACCESS^
       11 - редактирование картинок
   */
    function checkPermissions()
    {

        if(CMS_LEVEL_ACCESS<50) return true;


        if(CMS_LEVEL_ACCESS==51)
            if(in_array($this->frm['name'],array('models_bot','models_top','home'))) return true;

        if(CMS_LEVEL_ACCESS==52)
            if(in_array($this->frm['name'],array('models_bot','models_top','home','brands','pages'))) return true;

        if(CMS_LEVEL_ACCESS==53)
            if(in_array($this->frm['name'],array('avto2','home','models_bot','models_top', 'brands','pages', 'podbor_pages'))) return true;

        if(CMS_LEVEL_ACCESS==60)
            if(in_array($this->frm['name'],array('cnt','home','reviews'))) return true;

        if(CMS_LEVEL_ACCESS==99)
            if(in_array($this->frm['name'],array('cnt','home','brands','catalog_bot','catalog_top','gallery','models_bot','models_top'))) return true;

        die('[CP.checkPermissions]: Доступ запрещен');
    }

}