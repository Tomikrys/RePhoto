<?php

use yii\db\Migration;

class m171101_000000_data extends Migration
{
    const TABLE_ON_UPDATE = 'no action';
    const TABLE_ON_DELETE = 'no action';

    public function up()
    {
        $this->insert('user', [
            'first_name' => 'Martin',
            'last_name' => 'Sikora',
            'email' => 'admin@skrai.cz',
            'auth_key' => 'odO1EAamy1Rn1JIU2I3fRKaWHwiZSMUk',
            'password_hash' => '$2y$13$wVk9cA2kqRcRoTXR.Hy2/eOXFM1eZ7E7tSuyTUUJdBMcxQVQ4vK0i',
            'status' => 10,
            'role' => 1,
            'created_at' => date('timestamp'),
            'updated_at' => date('timestamp'),
        ]);

        $this->insert('user', [
            'first_name' => 'Martin',
            'last_name' => 'Sikora',
            'email' => 'client@skrai.cz',
            'auth_key' => 'odO1EAamy1Rn1JIU2I3fRKaWHwiZSMUk',
            'password_hash' => '$2y$13$wVk9cA2kqRcRoTXR.Hy2/eOXFM1eZ7E7tSuyTUUJdBMcxQVQ4vK0i',
            'status' => 10,
            'role' => 0,
            'created_at' => date('timestamp'),
            'updated_at' => date('timestamp'),
        ]);

        for ($i = 0; $i < 1; $i++){
            $this->addPlace($i);
        }

        /*
        # photo edited
        $this->insert('file', [
            'id_user' => 1,
            'extension' => 'png',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('photo_edited', [
            'id_user' => 1,
            'id_file' => 3,
            'id_photo_1' => 1,
            'id_photo_2' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        */
    }

    public function addPlace($i){
        $lat = rand(483051156547, 512428031341) / 10000000000;
        $lng = rand(113984983616, 192704407900) / 10000000000;

        //$lat = rand(-8500000000, 8500000000) / 100000000;
        //$lng = rand(-18000000000, 18000000000) / 100000000;

        # new place
        $this->insert('place', [
            'name' => 'Place ' . $i,
            'description' => '<p>Curabitur mattis nulla ultrices, porttitor risus vitae, venenatis nisi. Praesent condimentum blandit lacus. Aliquam aliquam, diam sit amet dignissim faucibus, mauris ipsum ultricies purus, quis ultricies sapien leo a purus. Suspendisse potenti. Cras non pulvinar ex, in auctor leo. Nunc ipsum erat, faucibus sed ante ut, condimentum rhoncus justo. Donec quam urna, facilisis ac nisl ac, auctor lobortis urna. Vivamus pellentesque ut libero eu vestibulum. Donec sit amet ligula sit amet turpis ornare ultricies ac id mauris. Ut non nisl quis lectus consequat scelerisque semper ac ipsum. Fusce id purus id ligula varius congue. In ut porttitor ex. Donec sed mauris eros. Donec aliquam fermentum neque, nec commodo felis rhoncus in. </p>',
            'latitude' => $lat,
            'longitude' => $lng,
            'id_user' => 1,
            'id_category' => rand(0, 2),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $placeID = Yii::$app->db->lastInsertID;
        # old photo
        $this->insert('file', [
            'id_user' => 1,
            'extension' => 'png',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $fileID = Yii::$app->db->lastInsertID;
        $this->insert('photo', [
            'name' => 'Place ' . $i . ' - old',
            'description' => '<p>Curabitur mattis nulla ultrices, porttitor risus vitae, venenatis nisi. Praesent condimentum blandit lacus. Aliquam aliquam, diam sit amet dignissim faucibus, mauris ipsum ultricies purus, quis ultricies sapien leo a purus. Suspendisse potenti. Cras non pulvinar ex, in auctor leo. Nunc ipsum erat, faucibus sed ante ut, condimentum rhoncus justo. Donec quam urna, facilisis ac nisl ac, auctor lobortis urna. Vivamus pellentesque ut libero eu vestibulum. Donec sit amet ligula sit amet turpis ornare ultricies ac id mauris. Ut non nisl quis lectus consequat scelerisque semper ac ipsum. Fusce id purus id ligula varius congue. In ut porttitor ex. Donec sed mauris eros. Donec aliquam fermentum neque, nec commodo felis rhoncus in. </p>',
            'latitude' => $lat,
            'longitude' => $lng,
            'aligned' => 1,
            'id_user' => 1,
            'id_file' => $fileID,
            'id_place' => $placeID,
            'visible' => 1,
            'captured_at' => rand(1940, 1965) . '-02-01',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        # new photo
        $this->insert('file', [
            'id_user' => 1,
            'extension' => 'png',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $fileID = Yii::$app->db->lastInsertID;

        $this->insert('photo', [
            'name' => 'Place ' . $i . ' - new',
            'description' => '<p>Ut in sodales arcu. Donec accumsan eleifend interdum. Suspendisse ex dolor, fringilla auctor luctus ac, viverra in velit. Vivamus ac nunc velit. Vivamus sagittis tincidunt malesuada. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam nunc mi, ornare vel nisi in, consectetur sagittis sem. </p>',
            'aligned' => 1,
            'latitude' => $lat,
            'longitude' => $lng,
            'id_user' => 1,
            'id_file' => $fileID,
            'id_place' => $placeID,
            'visible' => 1,
            'captured_at' => rand(1990, 2018) . '-02-01',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function down()
    {

    }
}
