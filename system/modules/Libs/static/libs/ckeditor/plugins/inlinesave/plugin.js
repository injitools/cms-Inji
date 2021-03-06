CKEDITOR.plugins.add('inlinesave',
        {
          init: function (editor)
          {
            editor.addCommand('inlinesave',
                    {
                      exec: function (editor)
                      {

                        var data = {};
                        data.data = editor.getData();
                        data.col = $(editor.container)[0].data('col');
                        data.model = $(editor.container)[0].data('model');
                        data.key = $(editor.container)[0].data('key');
                        jQuery.ajax({
                          type: "POST",
                          //Specify the name of the file you wish to use to handle the data on your web page with this code:
                          //<script>var dump_file="yourfile.php";</script>
                          //(Replace "yourfile.php" with the relevant file you wish to use)
                          //Data can be retrieved from the variable $_POST['editabledata']
                          //The ID of the editor that the data came from can be retrieved from the variable $_POST['editorID']

                          url: '/Ui/fastEdit/',
                          data: data

                        })

                                .done(function (data, textStatus, jqXHR) {

                                  alert("Все изменения были сохранены " + jqXHR.responseText + "");

                                })

                                .fail(function (jqXHR, textStatus, errorThrown) {

                                  alert("Error saving content. [" + jqXHR.responseText + "]");

                                });


                      }
                    });
            editor.ui.addButton('Inlinesave',
                    {
                      label: 'Save',
                      command: 'inlinesave',
                      icon: this.path + 'images/inlinesave.png'
                    });
          }
        });