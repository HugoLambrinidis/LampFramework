<?php

namespace Generator;

use src\connection;
use src\table;

class TableGenerator extends connection {

    public function __construct($tableName, $cols, $type)
    {
        $tableController = new table();
        $tableHasBeenCreated = $tableController->addTable($tableName, $cols, $type);
        if ( $tableHasBeenCreated ) {
            $model_path = self::getModelPath();
            $model_content = "<?php \n\n namespace Site\\Model;\n\nuse Controller\\AbstractController;\n".
                "use src\\queryBuilder;\n\nclass ".$tableName." extends AbstractController { \n\n ";
            foreach ($cols as $col) {
                $model_content .= "private $".$col."; \n";
            }
            $model_content .= "private \$tableName = \"$tableName\";\n\n private \$connection;\n\nprivate"
                ."\$queryBuilder;\n\npublic function __construct()\n{\n\$this->connection = self::getConnection();"
                ."\n\n \$this->queryBuilder = new queryBuilder();\n }\n\npublic function getAll(){\n\n return "
                ."\$this->queryBuilder->select(\$this->tableName)->sendQuery();\n}\n\npublic function getItemById(\$id)"
                ." { \nreturn \$this->queryBuilder->select(\$this->tableName)->where(\"id = \".\$id)->sendQuery();\n}\n"
                ."public function addItem(";
            for ($i= 0; $i < sizeof($cols); $i++) {
                if ( $i < sizeof($cols) -1 ) {
                    $model_content .= "\$".$cols[$i].", ";
                } else {
                    $model_content .= "\$".$cols[$i]."){\n";
                }
            }
            $model_content .= "\$this->queryBuilder->insert(\$this->tableName)->cols([";
            for($i = 0; $i < sizeof($cols); $i++) {
                if ($i < sizeof($cols) -1) {
                    $model_content .= "'".$cols[$i]."', ";
                } else {
                    $model_content .= "'".$cols[$i]."' ])";
                }
            }
            $model_content .= "->bindValues([";
            for ($i = 0; $i< sizeof($cols); $i++) {
                if ( $i < sizeof($cols) -1) {
                    $model_content .= "\$".$cols[$i].", ";
                } else {
                    $model_content .= "\$".$cols[$i]."])";
                }
            }
            $model_content .= "->sendQuery();\n}\n}";

            file_put_contents($model_path."/".$tableName.".php", $model_content);
        }
    }


}