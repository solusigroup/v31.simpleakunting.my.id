<?php

class Home extends Controller {
    public function index()
    {
        $data['judul'] = 'Halaman Utama';
        $this->view('templates/header', $data);
        $this->view('Dashboard/index', $data);
        $this->view('templates/footer');
    }
}