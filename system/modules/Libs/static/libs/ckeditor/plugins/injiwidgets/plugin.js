/* 
 * The MIT License
 *
 * Copyright 2015 Alexey Krupskiy <admin@inji.ru>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
var editors = [];
CKEDITOR.plugins.add('injiwidgets',
        {
          requires: ['iframedialog', 'fakeobjects'],
          init: function (editor)
          {
            editors.push(editor);
            var height = 300, width = 400;
            CKEDITOR.dialog.addIframe(
                    'injiwidgets',
                    'Inji Виджеты',
                    '/admin/widgets/widgetChooser', width, height,
                    function ()
                    {
                      var editor = CKEDITOR.currentInstance;
                      if (editor.getSelection().getSelectedElement()) {
                        var widget = (editor.getSelection().getSelectedElement()).data('cke-realelement');
                        $('#' + this.domId)[0].contentWindow.exist(decodeURIComponent(widget));
                      }

                    },
                    {
                      onOk: function ()
                      {
                        var editor = CKEDITOR.currentInstance;
                        var code = $('#' + this._.contents.iframe.undefined.domId)[0].contentWindow.genCode();
                        var match = code;

                        // Create document fragment from match
                        var realFragment = new CKEDITOR.htmlParser.fragment.fromHtml(match);

                        // Document fragments first child is the node we want
                        var realElement = realFragment && realFragment.children[ 0 ];
                        console.log(realElement);

                        // Element should have attributes but text nodes don't have them
                        // So we fake them
                        realElement.attributes = {};

                        // Create fake element
                        // @param element to replace
                        // @param className for fake element
                        // @param sets data-cke-real-element-type
                        // @param is resizable
                        var tmp = editor.createFakeParserElement(realElement, 'cke_inji_widget', 'inji_widget', false);
                        tmp.attributes.src = '/admin/widgets/widgetImage/?text=' + match;
// Because this is text rule we need to return string
                        // String can be returnded from writer
                        var writer = new CKEDITOR.htmlParser.basicWriter();

                        // set writer for node
                        tmp.writeHtml(writer);

                        // get html string from writer
                        var node = CKEDITOR.dom.element
                                .createFromHtml(writer.getHtml(), editor.document);
                        editor.insertElement(node);
                      }
                    }
            );

            editor.addCommand('injiwidgets', new CKEDITOR.dialogCommand('injiwidgets'));

            editor.ui.addButton('InjiWidgets',
                    {
                      label: 'Inji виджеты',
                      command: 'injiwidgets',
                      icon: this.path + 'images/logo-dark.png'
                    }
            );
            editor.on('doubleclick', function (evt)
            {
              var element = evt.data.element;

              if (element.is('img') && element.data('cke-real-element-type') == 'inji_widget')
              {
                evt.data.dialog = 'injiwidgets';
              }
            });
          },
          afterInit: function (editor)
          {
            var dataProcessor = editor.dataProcessor,
                    dataFilter = dataProcessor && dataProcessor.dataFilter;

            if (dataFilter)
            {
              dataFilter.addRules(
                      {
                        text: function (text)
                        {
                          return text.replace(/({WIDGET:[\s\S]*?})/ig, function (match)
                          {
                            // Create document fragment from match
                            var realFragment = new CKEDITOR.htmlParser.fragment.fromHtml(match);

                            // Document fragments first child is the node we want
                            var realElement = realFragment && realFragment.children[ 0 ];

                            // Element should have attributes but text nodes don't have them
                            // So we fake them
                            realElement.attributes = {};

                            // Create fake element
                            // @param element to replace
                            // @param className for fake element
                            // @param sets data-cke-real-element-type
                            // @param is resizable
                            var tmp = editor.createFakeParserElement(realElement, 'cke_inji_widget', 'inji_widget', false);
                            tmp.attributes.src = '/admin/widgets/widgetImage/?text=' + match;


                            // Because this is text rule we need to return string
                            // String can be returnded from writer
                            var writer = new CKEDITOR.htmlParser.basicWriter();

                            // set writer for node
                            tmp.writeHtml(writer);

                            // get html string from writer
                            return writer.getHtml();

                          });
                        }
                      });
            }
          }
        });