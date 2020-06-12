<?php

$data = function ($req, $res) {
  $id = $req->param('id');

  $res->output([
    'id' => $id
  ]);
};

$following = function ($req, $res) {
  $id = $req->param('id');

  $res->output([
    'id' => $id,
    'list' => [
      [
        'id' => 1241562626,
        'username' => 'test'
      ]
    ]
  ]);
};

return [
  ':id/data' => [
    'method'  => 'get',
    'handler' => $data
  ],
  ':id/following' => [
    'method'  => 'get',
    'handler' => $following
  ]
];
