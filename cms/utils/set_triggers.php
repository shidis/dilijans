<?
include('../auth.php');

$db=new DB();

Log_Tables::createWatchTrigger('cc_brand', array_diff($db->getColumns('cc_brand'), array('ti_id', 'ti_file_id')), 'brand_id');
echo 'cc_brand OK<br>';

Log_Tables::createWatchTrigger('cc_cat', array_diff($db->getColumns('cc_cat'), array('ti_id', 'ti_file_id', 'dt_upd', 'sc', 'bprice', 'cprice', 'upd_id')), 'cat_id');
echo 'cc_cat OK<br>';

Log_Tables::createWatchTrigger('cc_cat_img', array_diff($db->getColumns('cc_cat_img'), array()), 'id');
echo 'cc_cat_img OK<br>';

Log_Tables::createWatchTrigger('cc_class', array_diff($db->getColumns('cc_class'), array()), 'class_id');
echo 'cc_class OK<br>';

Log_Tables::createWatchTrigger('cc_cur', array_diff($db->getColumns('cc_cur'), array()), 'cur_id');
echo 'cc_cur OK<br>';

Log_Tables::createWatchTrigger('cc_dataset', array_diff($db->getColumns('cc_dataset'), array()), 'dataset_id');
echo 'cc_dataset OK<br>';

Log_Tables::createWatchTrigger('cc_dict', array_diff($db->getColumns('cc_dict'), array()), 'dict_id');
echo 'cc_dict OK<br>';

Log_Tables::createWatchTrigger('cc_dop', array_diff($db->getColumns('cc_dop'), array()), 'dop_id');
echo 'cc_dop OK<br>';

Log_Tables::createWatchTrigger('cc_extra', array_diff($db->getColumns('cc_extra'), array()), 'extra_id');
echo 'cc_extra OK<br>';

Log_Tables::createWatchTrigger('cc_gal', array_diff($db->getColumns('cc_gal'), array()), 'gal_id');
echo 'cc_gal OK<br>';

Log_Tables::createWatchTrigger('cc_min_extra', array_diff($db->getColumns('cc_min_extra'), array()), 'min_extra_id');
echo 'cc_min_extra OK<br>';

Log_Tables::createWatchTrigger('cc_model', array_diff($db->getColumns('cc_model'), array('ti_id', 'ti_file_id', 'dt_upd')), 'model_id');
echo 'cc_model OK<br>';

Log_Tables::createWatchTrigger('cc_mspez', array_diff($db->getColumns('cc_mspez'), array()), 'mspez_id');
echo 'cc_mspez OK<br>';

Log_Tables::createWatchTrigger('cc_suffix', array_diff($db->getColumns('cc_suffix'), array()), 'id');
echo 'cc_suffix OK<br>';

Log_Tables::createWatchTrigger('cc_sup', array_diff($db->getColumns('cc_sup'), array()), 'sup_id');
echo 'cc_sup OK<br>';

Log_Tables::createWatchTrigger('cc_suplr', array_diff($db->getColumns('cc_suplr'), array()), 'suplr_id');
echo 'cc_suplr OK<br>';

Log_Tables::createWatchTrigger('cc_tag', array_diff($db->getColumns('cc_tag'), array()), 'tag_id');
echo 'cc_tag OK<br>';

Log_Tables::createWatchTrigger('cc_tag_group', array_diff($db->getColumns('cc_tag_group'), array()), 'tag_group_id');
echo 'cc_tag_group OK<br>';

Log_Tables::createWatchTrigger('cii_file', array_diff($db->getColumns('cii_file'), array('param', 'CM', 'SID')), 'file_id');
echo 'cii_file OK<br>';

Log_Tables::createWatchTrigger('ci_file', array_diff($db->getColumns('ci_file'), array('param', 'col_model')), 'file_id');
echo 'ci_file OK<br>';

Log_Tables::createWatchTrigger('cp_menu', array_diff($db->getColumns('cp_menu'), array()), 'menu_id');
echo 'cp_menu OK<br>';

Log_Tables::createWatchTrigger('cu_users', array_diff($db->getColumns('cu_users'), array()), 'userId');
echo 'cu_users OK<br>';

Log_Tables::createWatchTrigger('gl_image', array_diff($db->getColumns('gl_image'), array()), 'image_id');
echo 'gl_image OK<br>';

Log_Tables::createWatchTrigger('gl_topic', array_diff($db->getColumns('gl_topic'), array()), 'topic_id');
echo 'gl_topic OK<br>';

Log_Tables::createWatchTrigger('os_dc', array_diff($db->getColumns('os_dc'), array()), 'dc_id');
echo 'os_dc OK<br>';

Log_Tables::createWatchTrigger('os_dop', array_diff($db->getColumns('os_dop'), array()), 'dop_id');
echo 'os_dop OK<br>';

Log_Tables::createWatchTrigger('os_item', array_diff($db->getColumns('os_item'), array()), 'item_id');
echo 'os_item OK<br>';

Log_Tables::createWatchTrigger('os_order', array_diff($db->getColumns('os_order'), array()), 'order_id');
echo 'os_order OK<br>';

Log_Tables::createWatchTrigger('os_user', array_diff($db->getColumns('os_user'), array()), 'user_id');
echo 'os_user OK<br>';

Log_Tables::createWatchTrigger('os_slog', array_diff($db->getColumns('os_slog'), array()), 'id');
echo 'os_slog OK<br>';

Log_Tables::createWatchTrigger('os_files', array_diff($db->getColumns('os_files'), array()), 'id');
echo 'os_files OK<br>';

Log_Tables::createWatchTrigger('ss_cnt', array_diff($db->getColumns('ss_cnt'), array()), 'cnt_id');
echo 'ss_cnt OK<br>';

Log_Tables::createWatchTrigger('ss_cnt_type', array_diff($db->getColumns('ss_cnt_type'), array()), 'cnt_type_id');
echo 'ss_cnt_type OK<br>';

Log_Tables::createWatchTrigger('ss_img', array_diff($db->getColumns('ss_img'), array()), 'img_id');
echo 'ss_img OK<br>';

Log_Tables::createWatchTrigger('ss_news', array_diff($db->getColumns('ss_news'), array()), 'news_id');
echo 'ss_news OK<br>';

Log_Tables::createWatchTrigger('ss_news_group', array_diff($db->getColumns('ss_news_group'), array()), 'news_group_id');
echo 'ss_news_group OK<br>';

Log_Tables::createWatchTrigger('ss_pages', array_diff($db->getColumns('ss_pages'), array()), 'page_id');
echo 'ss_pages OK<br>';

Log_Tables::createWatchTrigger('ss_pages_blocks', array_diff($db->getColumns('ss_pages_blocks'), array()), 'block_id');
echo 'ss_pages_blocks OK<br>';

Log_Tables::createWatchTrigger('system_data', array_diff($db->getColumns('system_data'), array()), 'data_id');
echo 'system_data OK<br>';

Log_Tables::createWatchTrigger('ss_pages_blocks', array_diff($db->getColumns('ss_pages_blocks'), array()), 'block_id');
echo 'ss_pages_blocks OK<br>';

Log_Tables::createWatchTrigger('reviews', array_diff($db->getColumns('reviews'), array()), 'id');
echo 'reviews OK<br>';

