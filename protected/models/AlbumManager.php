<?php

class AlbumManager {

    //TODO: re-implement saving image process.
    public function saveAlbumImages($userId) {
        //TODO: implement saveImages() to return boolean.
        $albumImage = new AlbumImage();
        $albumImage->user_id = $userId;
        $ret = $albumImage->saveImages($userId);

        return $ret;
    }

    /**
     * Delete item image based on item image id
     * @param int $id
     */
    public function deleteAlbumImage(AlbumImage $albumImage) {
        return $albumImage->deleteModel(true);
    }

    public function loadAlbumImagesByUserId($userId) {
        return AlbumImage::model()->getAllByUserId($userId);
    }

}

