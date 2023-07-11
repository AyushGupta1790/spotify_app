<?php

use Phalcon\Mvc\Controller;
use MyApp\Models\Playlists;

class MyController extends Controller
{
    public function indexAction()
    {
        $playlist = $this->db->fetchAll(
            "SELECT * FROM playlists",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $view = '';
        foreach ($playlist as $key => $value) {
            $type=$value['type'].'s';
            $data = $this->getData->getById($value['spotify_id'], $type);
            if (empty($data['images'])) {
                $img = 'https://i.scdn.co/image/ab6761610000e5eb8de0e6e7e55d7773931ab7f4';
            } else {
                $img = $data['images'][0]['url'];
            }
            if ($value['type'] == 'tracks') {
                $img = $data['album']['images'][0]['url'];
            }
            $view .= "<div class='card col-3 p-3 border  border-5 border-white bg-dark text-light mb-1'
                 style='width: 18rem;height:330px;border-radius:10px; '>
        <img class='card-img-top' src=' " . $img . " ' alt='Card image cap' height=180px>
        <div class='card-body'>
          <h6 class='card-title'>$data[name]</h6>
          <a href='my/delete?delid=$value[id]' class='btn btn-primary border border-white'>Delete $value[type]</a>
        </div>
      </div>";
        }
        $this->view->view = $view;
    }
    public function deleteAction()
    {
        $id = $this->request->get('delid');
        $playlists = Playlists::findFirst($id);
        $playlists->delete();
        $this->response->redirect('my');
    }
}
