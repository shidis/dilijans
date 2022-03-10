<?php
@define(true_enter, 1);
include $_SERVER['DOCUMENT_ROOT'] . '/config/init.php';

class MigrationEntries
{
    private $db;
    private $arFormattedEntries = [];
    public $message = '';

    public function __construct()
    {
        $db = new DB();
        $this->db = $db;
    }

    private function getEntriesFromNewsTable()
    {
        $arFormattedEntries = [];
        $arEntries = $this->db->fetchAll("SELECT * FROM `ss_news`");


        foreach ($arEntries as $entire) {
            $arFormattedEntries[] = [
                'entry_section_id' => 5,
                'title' => $entire['title'],
                'sname' => $entire['sname'],
                'intro' => $entire['intro'],
                'link' => $entire['link'],
                'text' => $entire['text'],
                'description' => $entire['description'],
                'keywords' => $entire['keywords'],
                'dt_added' => $entire['dt_added'],
                'dt' => $entire['dt'],
                'img1' => $entire['img1'],
                'img2' => $entire['img2'],
                'published' => $entire['published'],
            ];
        }

        $this->arFormattedEntries = $arFormattedEntries;

        $k = 33;
    }

    private function addEntriesToEntryTable()
    {
        foreach ($this->arFormattedEntries as $entry) {
            $this->db->insert('entry', $entry);
        }
    }

    public function start()
    {
        $this->getEntriesFromNewsTable();
        $this->addEntriesToEntryTable();
        $this->message = 'Migration done.';
    }
}
?>

<form method="post" action="">
    <input type="submit" name="start" value="start">
</form>

<? if (isset($_POST['start'])){
    $migration = new MigrationEntries();
    $migration->start();
    echo $migration->message;
}
