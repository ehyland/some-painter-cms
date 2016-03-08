<?php
class JSONController extends Controller{

    public function sendResponse ($data, $status = 200, $description = "OK", $contentType = "application/json" ) {

        if (is_array($data)) {
            $data = json_encode($data);
        }

        return $this->response
            ->setStatusCode($status, $description)
            ->addHeader('Content-Type', $contentType)
            ->setBody($data);
    }
}
