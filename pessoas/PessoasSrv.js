pessoas
    .factory('PessoasSrv', function($resource){
        return $resource(
            '/public/pessoas/:id', {
                id: '@id'
            },
            {
                update:{
                    method: 'PUT',
                    url: '/public/pessoas/:id'
                }
            }
        );
    });