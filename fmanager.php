<?php

class EntryPointManager
{   
    protected const TYPE_FOLDER = 'dir';
    protected const TYPE_FILE = 'file';

    protected $fileManager;
    protected $actionList = ['getFileList', 'getInfo', 'remove', 'edit', 'save', 'download'];
    protected $notEditableExt = [
        'gif', 'png', 'ico', 'jpg', 'jpeg', 'bmp', 'ttf', 'eot', 'woff', 
        'woff2', 'mp3', 'wav', 'mp4', 'avi',
    ];
    protected $codeViewExt = [
        'css', 'scss', 'h', 'js', 'json', 'php', 'phtml', 'py', 'rs', 
        'sql', 'svg', 'toml', 'tpl', 'ts', 'twig', 'vue', 'xml', 'yaml',
    ];
    protected $useAceEditor = true;
    protected $usePrismHighlight = true;
    protected $logo = 'data:image/png;base64,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';

    public function __construct(FileManager $fm)
    {   
        $this->fileManager = $fm;
    }
    
    public function handleRequest()
    {
        if (!empty($_REQUEST['action'])) {
            $action = $_REQUEST['action'];

            if (in_array($action, $this->actionList)) {
                $this->$action();
            }
        } else {
            header('Location: ?action=getFileList');
            exit();
        }
    }

    protected function getFileList()
    {   
        $path = '';
        $isImageScript = false;

        if (!empty($_REQUEST['path'])) {  
            $path = $_REQUEST['path'];
        }

        $this->handlePost($path);
        $output[] = '<style>body,html{font-family:arial;font-size:14px}.right{color:#cc8c16}'
            . '.chmod{color:#aaa;}.owner{color:#24bf7c}.size{color:#7a7;}'
            . '.right,.chmod,.owner,.size{font-size:13px;white-space: nowrap;}'
            . '.thumb span{position:absolute;visibility:hidden;background:#fff;border:1px solid #ccc;}'
            . '.thumb:hover, .thumb:hover span{visibility:visible;top:0;z-index:1;}'
            . '.thumb img{max-width:100px}'
            . '[type="submit"]{cursor:pointer;height:24px;padding:4px 8px;border:1px solid #ccc;background:#eee;}'
            . '[type="submit"]:hover{background:#ddd}'
            . '[type="text"]{height:24px;padding:4px;width:100px}</style>' 
            . '<div><img style="vertical-align:middle;margin-right: 184px;" src="' . $this->logo . '"/><form method="POST" enctype="multipart/form-data" '
            . ' style="display:inline-block"> 📤 '
            . '<input type="file" name="file" /> '
            . '<input type="submit" value=" Upload "></form></div>'
            . '<div><form method="POST" style="display:inline-block">'
            . '<label title="Directory">📂</label> '
            . '<input type="text" name="directory" placeholder="folder" /> '
            . '<input type="submit" value=" create "></form> '
            . '<form method="POST" style="display:inline-block;margin-left:20px;margin-right:20px"><label title="File">📑</label> '
            . '<input type="text" name="filename" placeholder="file" /> '
            . '<input type="submit" value=" create "></form>'
            . '<form method="POST" style="display:inline-block;">🔀 '
            . '<input type="text" name="oldname" placeholder="current name" /> <input type="text" name="newname" placeholder="new name"/>'
            . ' <input type="submit" value=" rename ">'
            . '</form></div>';
        $output[] = $this->makeReturnPath($path);
        $list = $this->fileManager->getFolderContent($path);
        $output[] = '<form id="form-list" method="POST"><table style="padding:10px;background:#f9f9f9;margin-top:5px;">';

        foreach ($list as $type => $entries) {
            if (self::TYPE_FOLDER === $type) {
                foreach ($entries as $name) {
                    $realPath = (!empty($path) ? ($path . '/') : '') . $name ;
                    $perms = $this->fileManager->getFilePerms($realPath);
                    $output[] = '<tr>'
                        . '<td>'
                        . '<span style="background:#65BDFB;color:#fff;padding:0 3px;white-space: nowrap;">DIR</span> <a href="' 
                        . '?action=getFileList&path=' . urlencode($realPath) 
                        . '" style="font-family:Tahoma;color:#3A35B8FF">' . $name . '</a>'
                        . '</td>'
                        . '<td class="chmod">&nbsp;' . $perms[0] . '&nbsp;</td>'
                        . '<td class="right">&nbsp;' . $perms[1] . '&nbsp;</td>'
                        . '<td class="owner">&nbsp;' . $perms[2] . '&nbsp;</td>'
                        . '<td>&nbsp;</td>'
                        . '<td><a href="?action=download&path=' . urlencode($realPath) . '" style="padding-left:20px;text-decoration:none;" title="download ZIP">⬇️</a></td>'
                        . '<td></td>'
                        . '<td>'
                        . '<a href="?action=remove&path=' . urlencode($realPath) . '" onclick="'
                        . 'if (!confirm(\'Are you sure to delete < ' . $name . ' >?\'))'
                        . '{return false;}"'
                        . ' style="color:#f00;font-size:10px;margin-left:60px" title="Delete">del</a>'
                        . '</td>'
                        . '<td><span style="color:#777;margin-left:20px;font-family: monospace;white-space: nowrap;">' 
                        . $this->fileManager->getDateModified($realPath) 
                        . '</span></td>'
                        . '<td></td>'
                        . '</tr>';
                }
            } elseif (self::TYPE_FILE === $type) {
                foreach ($entries as $name) {
                    $isImage = preg_match('#\.(gif|png|jpg|jpeg|svg)$#i', $name);

                    if ($isImage) {
                        $isImageScript = true;
                    }
                    
                    $realPath = (!empty($path) ? ($path . '/') : '') . $name ;
                    $perms = $this->fileManager->getFilePerms($path);
                    $pathinfo = pathinfo($realPath);
                    $ext = $pathinfo['extension'] ?? '';
                    $editableLink = !in_array($ext, $this->notEditableExt) 
                        ? ('<a href="?action=edit&path=' . urlencode($realPath) . '" style="padding-left:20px;color:#54BCFEFF" title="Edit">edit</a>') 
                        : '';
                    
                    $output[] = '<tr>'
                        . '<td style="position:relative;">'
                        . '<a href="' 
                         . '?action=getInfo&log=' . urlencode($realPath)  
                        . '" style="color:#0D6DAEFF" class="' . ($isImage ? 'thumb' : '') . '" ' . ($isImage ? (' data-hover-src="?action=getInfo&log=' . urlencode($realPath) . '"') : '') . '>' . $name 
                        . ($isImage ? (' <span><img src="" /></span>') : '')
                        . '</a>'
                        . '</td>'
                        . '<td class="chmod">&nbsp;' . $perms[0] . '&nbsp;</td>'
                        . '<td class="right">&nbsp;' . $perms[1] . '&nbsp;</td>'
                        . '<td class="owner">&nbsp;' . $perms[2] . '&nbsp;</td>'
                        . '<td>&nbsp' . $editableLink . '</td>'
                        . '<td><a href="?action=download&path=' . $realPath . '" style="padding-left:20px;text-decoration:none;" title="download">⬇️</a></td>'
                        . '<td><input type="checkbox" name="files[]" value="' . $name . '" onclick="document.querySelector(\'#delete-submit\').style.display = document.querySelectorAll(\'#form-list input[type=checkbox]:checked\').length ? \'block\' : \'none\';"></td>'
                        . '<td>'
                        . '<a href="?action=remove&path=' . urlencode($realPath) . '" onclick="'
                        . 'if (!confirm(\'Are you sure to delete < ' . $name . ' >?\'))'
                        . '{return false;}"'
                        . ' style="color:#f00;font-size:10px;margin-left:60px" title="Delete">del</a>'
                        . '</td>'
                        . '<td><span style="color:#777;margin-left:20px;font-family: monospace;white-space: nowrap;">' 
                        . $this->fileManager->getDateModified($realPath) 
                        . '</span></td>'
                        . '<td class="size"><span style="margin-left:10px">' . $this->fileManager->getSize($realPath) . '</td>'
                        . '</tr>';
                }
            } 
        }

        $output[] = '<tr><td colspan="6"></td><td colspan="2" style="padding-top:8px;padding-bottom:8px"><input id="delete-submit" type="submit" value="Delete Selected" style="display:none;padding:8px 4px;box-sizing: content-box;" onclick="if (!confirm(\'Are you sure to delete selected items\')){return false;}else{var el=document.querySelector(\'#form-list\');var inp=document.createElement(\'input\');inp.type=\'hidden\';inp.value=\'1\';inp.name=\'mass_delete\';el.appendChild(inp);}" /></td></tr>';
        $output[] = '</table></form>';

        if ($isImageScript) {
            $output[] = "<script>const triggers = document.querySelectorAll('.thumb');
                triggers.forEach(trigger => {
                    trigger.addEventListener('mouseenter', () => {
                        const hoverImage = trigger.querySelector('img');
                        console.log(hoverImage.getAttribute('src'));
                        if (!hoverImage.getAttribute('src')) {
                            hoverImage.src = trigger.getAttribute('data-hover-src');
                        }
                        trigger.classList.add('is-hovered'); 
                    });
                });</script>";
        }

        file_put_contents('php://output', implode(' ', $output));
    }

    protected function getInfo()
    {
        $log = !empty($_REQUEST['log']) ? $_REQUEST['log'] : '';
        $filePath =  $this->fileManager->root . '/' . $log;

        if (file_exists($filePath)) {
            $pathinfo = pathinfo($filePath);
            
            if (
                isset($pathinfo['extension']) 
                && in_array($pathinfo['extension'], $this->codeViewExt)
            ) {
                $info = '<!DOCTYPE html><html><head></head><body>';

                if ($this->usePrismHighlight) {
                    $info .= '<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.30.0/themes/prism-coy.min.css" rel="stylesheet" />';
                }

                $info .= '<style>';

                if ($this->usePrismHighlight) {
                    $info .= 'code{margin:0;padding:0;font-family: Verdana monospace !important;font-size:16px;line-height:20px;white-space: pre;display:block;}';
                } else {
                    $info .= 'code{margin:0;padding:0;font-family: Verdana monospace;font-size:16px;line-height:20px;height:20px;white-space: pre;display:block;}}';
                }

                $info .= 'htm,body{font-family: Arial;font-size:14px;line-height:20px;padding:0;margin:0}'
                    . 'td{vertical-align:top}'
                    . '.line{padding-left:5px;line-height:20px;height:20px;}.gutter .line{color: grey}'
                    . ':not(pre) > code[class*="language-"], pre[class*="language-"] {background: none;}'
                    . 'pre[class*="language-"] {padding:0;margin:0;}'
                    . 'code[class*="language-"]{padding:0;padding-top:1px}'
                    . 'code[class*="language-"], pre[class*="language-"]{line-height:20px;padding-left:5px;}'
                    . 'pre[class*="language-"] > code{margin-top:2px;background-size: 40px 40px;}'
                    . 'pre[class*="language-"]::after, pre[class*="language-"]::before{box-shadow:none;}'
                    . '</style>'
                    . '<p style="color:#54BCFEFF;padding-left:10px">' . $this->makeReturnPath($log, false) . '</p>'
                    . '<table><tr>';
                $lines = [];
                $lines = file($filePath);
                $count = count($lines);

                if ($count === 1) {
                    $lines = explode("\r", $lines[0]);
                    $count = count($lines);
                }

                $info .= '<td class="gutter">';

                for ($i = 1; $i <= $count; $i++) {
                    $info .= '<div class="line" style="text-align:right;padding-right:5px;border-right: solid 2px green;"><span style="margin-top:2px;display:inline-block">' . $i . '</span></div>';
                }
                
                $info .= '</td>';
                
                if ($this->usePrismHighlight) {
                    $info .= '<td><pre><code class="language-' . $pathinfo['extension'] . '">';

                    foreach ($lines as $line) {
                        $info .= htmlentities($line);
                    }

                    $info .= '</code></pre>';
                } else {
                    $info .= '<td class="code">';

                    foreach ($lines as $line) {
                        $info .= '<div class="line"><code>' . htmlentities($line) . '</code></div>';
                    }
                }

                $info .= '</td></tr></table><br>';
                
                if ($this->usePrismHighlight) {
                    $info .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.30.0/prism.min.js"></script>';
                    $info .= '<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/plugins/autoloader/prism-autoloader.min.js"></script>';
                }
                
                $info .= '</body></html>';
                file_put_contents('php://output', $info);
            } else {
                $content = file_get_contents($filePath);

                if (
                    (
                        isset($pathinfo['extension']) 
                        && !in_array($pathinfo['extension'], $this->notEditableExt)
                    ) || !isset($pathinfo['extension'])
                ) {
                    $info = '<p style="color:#54BCFEFF;background-color:#efefef;padding:10px">' . $this->makeReturnPath($log, false) . '</p>';
                    $info .= '<pre style="padding:10px">' . htmlentities($content) . '</pre><br>';
                } else {
                    $info = $content;
                    header('Content-Type: ' . Mime::getType($pathinfo['basename']));
                }

                ob_clean();
                file_put_contents('php://output', $info);
            }
        }
    }


    protected function remove()
    {
        $path = getRequestParam('path');

        if ($path) {
            $file = $this->fileManager->root . '/' . $path;

            if (file_exists($file) && is_file($file)) {
                $this->fileManager->deleteFile($path);
            } elseif (is_dir($file)) {
                $this->fileManager->deleteFolder($path);
            }
        }

        header('Location: ?action=getFileList&path=' . rtrim(preg_replace('#[^\/]*$#', '', $path), '/'));
        exit();
    }

    protected function edit()
    {
        $output = 'Prohibited';
        $path = getRequestParam('path');
        $basename = basename($path);
        $file = $this->fileManager->root . '/' . $path;

        if (file_exists($file) && is_file($file)) {
            $postFile = filter_input(INPUT_POST, 'file');

            if ($postFile !== false && !is_null($postFile)) {
                $postFile = str_replace(["\r"], '', $postFile);
                file_put_contents($file, $postFile);
                $pos = strrpos($path, '/');

                if ($pos !== false) {
                    $path = substr($path, 0, -(strlen($path) - $pos));
                } else {
                    $path = '';
                }

                header('Location: ?action=getFileList&path=' . $path);
                exit();
            }

            $content = file_get_contents($file);
            
            if ($this->useAceEditor) {
                $output = '<p style="color:#54BCFEFF;">' . $this->makeReturnPath($path, false) . '</p>'
                    . '<form method="POST">' 
                    . '<div>'
                    . '<div id="editor">' . htmlspecialchars($content) . '</div>'
                    . '<textarea style="display:none" name="file">'
                    . htmlspecialchars($content) 
                    . '</textarea></div>'
                    . '<div style="margin-top:15px;">'
                    . '<input type="submit" name="submit" value=" SAVE " '
                    . 'style="padding:10px 20px;cursor:pointer" /></div>'
                    . '</form>';
                $output .= '
                    <style>
                        .ace_editor {height: 600px;width:90%;border:1px solid #aaa}
                    </style>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/ace.min.js" type="text/javascript" charset="utf-8"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/mode-php.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/mode-javascript.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/worker-php.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/worker-javascript.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/theme-chrome.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/ext-searchbox.min.js"></script>
                    <script>
                        var editor = ace.edit("editor");
                        editor.setTheme("ace/theme/chrome");
                        editor.session.setMode("ace/mode/' . (strpos($basename, '.js') ? 'javascript' : 'php') . '");
                        var textarea = document.querySelector(\'textarea[name="file"]\');
                        editor.getSession().on("change", function () {
                            textarea.value = editor.getSession().getValue();
                        });
                        ace.config.loadModule("ace/ext/searchbox", function(m) {m.Search(editor)});
                        editor.searchBox.hide();
                    </script>
                ';
            } else {
                $output = '<p style="color:#54BCFEFF;">' . $this->makeReturnPath($path, false) . '</p>'
                    . '<form method="POST">' 
                    . '<div><textarea style="width:100%;min-height:600px;border:1px solid #aaa;'
                    . 'padding:10px;box-sizing:border-box;" name="file">'
                    . htmlspecialchars($content)
                    . '</textarea></div>'
                    . '<div style="margin-top:15px;">'
                    . '<input type="submit" name="submit" value=" SAVE " '
                    . 'style="padding:10px 20px;cursor:pointer" /></div>'
                    . '</form>';
            }
        }

        file_put_contents('php://output', $output);
    }

    /**
    * Download
    *
    */
    protected function download()
    {
        $path = getRequestParam('path', '');
        $file = $this->fileManager->root . '/' . $path;

        if (file_exists($file) && is_file($file)) {
            $this->fileManager->downloadFile($file);
        } elseif (file_exists($file) && is_dir($file)) {
            $this->fileManager->downloadZipFolder($path);
        } else {
            header('Location: ?action=getFileList&path=' . rtrim(preg_replace('#[^\/]*$#', '', $path), '/'));
            exit();
        }
    }

    protected function handlePost($path)
    {
        if (!empty($_FILES) && array_key_exists('file', $_FILES)) {
            $this->fileManager->uploadFile($path);
        }

        if (empty($_POST)) {
            return;
        }

        if (array_key_exists('filename', $_POST)) {
            $filename = filter_input(INPUT_POST, 'filename');
            $filename = trim($filename);

            if (empty($filename)) {
                return;
            }

            if (empty($path)) {
                $file = $this->fileManager->root . '/' . $filename;
            } else {
                $file = $this->fileManager->root . '/' . $path . '/' . $filename;
            }

            if (file_exists($file)) {
                return;
            }

            $h = fopen($file, 'w');

            if ($h) {
                fclose($h);
            }
        }

        if (array_key_exists('directory', $_POST)) {
            $dirname = filter_input(INPUT_POST, 'directory');
            $dirname = trim($dirname);

            if (empty($dirname)) {
                return;
            }
             
            $this->fileManager->createFolder($path, $dirname);
        }

        if (
            array_key_exists('oldname', $_POST) 
            && array_key_exists('newname', $_POST)
        ) {
            $oldname = trim(filter_input(INPUT_POST, 'oldname'));
            $newname = trim(filter_input(INPUT_POST, 'newname'));

            if (!empty($oldname) && !empty($newname)) {
                $oldname = (!empty($path) ? (rtrim($path, '/') . '/') : '') . $oldname;
                $this->fileManager->rename($oldname, $newname);
            }
        }

        if (array_key_exists('mass_delete', $_POST) && !empty($_POST['files']) && is_array($_POST['files'])) {
            foreach ($_POST['files'] as $filename) {
                if (empty($path)) {
                    $file = $this->fileManager->root . '/' . $filename;
                } else {
                    $file = $this->fileManager->root . '/' . $path . '/' . $filename;
                }

                unlink($file); 
            }
        }
    }

    protected function makeReturnPath($path, bool $isDir = true): string
    {
        $result = '';
        $path = trim($path);
        $breadCrumbs = [];
        $temp = [];

        if (!empty($path)) {
            $arr = explode('/', $path);
            array_pop($arr);
            $href = '?action=getFileList';

            if (count($arr) > 0) {
                $href .= '&path=' . implode('/', $arr);
            }

            $arr = explode('/', $path);

            if ($isDir) {
                foreach ($arr as $pathName) {
                    $temp[] = $pathName;
                    $breadCrumbs[] = '<a style="color:#54BCFEFF;" href="?action=getFileList&path=' . urlencode(implode('/', $temp)) . '">' . $pathName . '</a>';
                }

                $result = '<a href="' . $href . '" style="font-family:Tahoma;text-decoration:none">🔙</a> <i style="color:#54BCFEFF;margin-left:20px">' . implode('/', $breadCrumbs) . '</i> <br>';
            } else {
                $fileName = array_pop($arr);

                foreach ($arr as $pathName) {
                    $temp[] = $pathName;
                    $breadCrumbs[] = '<a style="color:#54BCFEFF;" href="?action=getFileList&path=' . urlencode(implode('/', $temp)) . '">' . $pathName . '</a>';
                }

                $result = '<a href="' . $href . '" style="font-family:Tahoma;text-decoration:none">🔙</a> <i style="color:#54BCFEFF;margin-left:20px">' . implode('/', $breadCrumbs) . '/' . $fileName . '</i>';
            }
        }

        return $result;
    }
}

class FileManager
{
    public $root;
    public $defaultFolderClass = ['open' => 'copen', 'close' => 'cclose'];
    public $defaultFileClass = 'cfile';
    public $classCss = [
        'txt' => 'ctext',
        'rtf' => 'ctext',
        'doc' => 'cdoc',
        'xls' => 'cxls',
        'pdf' => 'cpdf',
        'jpg' => 'cjpg',
        'png' => 'cpng',
        'gif' => 'cgif',
        'avi' => 'cavi',
        'wmf' => 'cwmf',
        'mov' => 'cmov',
        'flv' => 'cflv',
        'mp3' => 'cmp3',
        'wav' => 'cwav',
    ];

    public function __construct($rootPath)
    {
        $this->root = $rootPath;
    }

    public function getFolderContent($path): array
    {
        $list = [];
        $absPath = $this->root . '/' . $path;
        if (is_dir($absPath)) {
            $h = opendir($absPath);

            if ($h) {
                $dirs = [];
                $files = [];

                while (false !== ($entry = readdir($h))) {
                    if ($entry === '.' || $entry === '..') {
                        continue;
                    }

                    $entryPath = $absPath . '/' . $entry;

                    if (is_dir($entryPath)) {
                        $dirs[] = $entry;
                    } else {
                        $files[] = $entry;
                    }
                }

                natsort($dirs);
                natsort($files);

                if (count($dirs) > 0 || count($files) > 0) {
                    $list = [
                        'dir' => $dirs,
                        'file' => $files
                    ];
                }
            }
        }

        return $list;
    }

    public function getTree($path, &$tree, &$errors, $last = '')
    {
        if (!is_dir($this->root)) {
            $errors[] = 'Root folder ' . $this->root . 'does not exist';
            return;
        }

        $absPath = $this->root . '/' . $path;

        if (is_dir($absPath)) {
            $dir = dir($absPath);
            $temp = [];

            while (false !== ($entry = $dir->read())) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $entryPath = $absPath . '/' . $entry;

                if (is_dir($entryPath)) {
                    $type = 'dir';
                    $class = $entry === $last 
                        ? $this->defaultFolderClass['open'] 
                        : $this->defaultFolderClass['close'];
                } else {
                    $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                    $class = isset($this->classCss[$ext]) 
                        ? $this->classCss[$ext] 
                        : $this->defaultFileClass;
                    $type = 'file';
                }

                $temp[] = [
                    'name' => (!empty($path) ? ($path.'/') : '').$entry,
                    'type' => $type,
                    'class' => $class,
               ];
            }
            
            $dir->close();
            $tree[] = $temp;
        } elseif (!is_file($absPath)) {
            $errors[] = $path . 'does not exist';
        }

        $pathInfo = explode('/', $path);

        if (!empty($path)) {
            $last = $pathInfo[count($pathInfo) - 1];
            unset($pathInfo[count($pathInfo) - 1]);
            $this->getTree(implode('/', $pathInfo), $tree, $errors, $last);
        } else {
            $tree = array_reverse($tree);
        }
    }

    public function createFolder($path, $newfolder)
    {
        if (empty($path)) {
            $absPath = $this->root . '/'  . $newfolder;
        } else {
            $absPath = $this->root . '/' . $path . '/' . $newfolder;
        }
        
        if (is_dir($absPath)) {
            return;
        }

        return @mkdir($absPath, 0777);
    }

    public function deleteFolder($path): bool
    {
        $absPath = $this->root . '/' . $path;
        $result = false;

        if ($handle = opendir($absPath)) {
            $entities = scandir($absPath);

            foreach ($entities as $entity) {
                if ($entity !== '.' && $entity !== '..') {
                    if (is_dir($absPath . '/' . $entity)) {
                        // Empty directory? Remove it
                        if (!@rmdir($absPath . '/' . $entity)) { 
                            // Not empty? Delete the files inside it
                            $this->deleteFolder($path . '/' . $entity); 
                        }
                    } else {
                        @unlink($absPath . '/' . $entity);
                    }
                }
            }

            closedir($handle);
            @rmdir($absPath);
            $result = true;
        }

        return $result;
    }

    public function deleteFile($path)
    {
        $absPath = $this->root . '/' . $path;
        @unlink($absPath);
    }

    public function rename($path, $newname)
    {
        $newname = str_replace(['/', '\\'], ['', ''], $newname);
        $absPath = $this->root . '/' . $path;
        $pos = strrpos($absPath, '/');
        $targetPath = substr($absPath, 0, $pos) . '/' . $newname;

        return rename($absPath, $targetPath);
    }

    public function downloadZipFolder($path, $tempFolderPath = '/tmp'): bool
    {
        $dirName = basename($path);
        $absPath = $this->root . '/' . $path;
        $result = false;
        $zipFile = $dirName . '.zip';
        $tempFile = rtrim($tempFolderPath, '/') . '/' . $zipFile;
        $isAddedToZip = false;
        
        $zip = new Zip();

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $isAddedToZip = $zip->addDir($absPath, $this->root);
            $zip->close();
        } else {
            echo 'Cant open ' . $tempFile;
        }

        if ($isAddedToZip) {
            $result = $this->downloadFile($tempFile, $zipFile);
            unlink($tempFile);
            exit();
        }

        return $result;
    }

    public function downloadFile($file, $newFileName = '')
    {
        $handle = @fopen($file, 'rb');

        if (!$handle) {
            return false;
        }
        
        header_remove();

        if (preg_match('/^http/', $file)) {
            $headers = get_headers($file, 1);
            $headers = array_change_key_case($headers);

            if (!empty($headers['content-length'])) {
                header('Content-Length: ' . $headers['content-length']);
            }
        } else {
            header('Content-Length: ' . filesize($file)); 
        }

        if (empty($newFileName)) {
            $newFileName = basename($file);
        }
        
        ob_end_clean();

        header('Content-Type: ' . mime_content_type($file));
        header('Content-Disposition: attachment; filename=' .  $newFileName);
        header('Cache-Control: no-cache');
        header('Pragma: public');
        header('Expires: 0');
        header('Access-Control-Allow-Origin: *');
        
        if ($handle) {
            fpassthru($handle);
            fclose($handle);
        }

        return true;
    }

    public function copyRemoteFile($url, $toFile, &$error = '')
    {
        $toFile = $this->root . '/' . ltrim($toFile, '/');

        if (($h = fopen($url, 'r')) === false) {
            $error = 'Can\'t open remote resource ' . $url;
            return;
        }

        fclose($h);

        if (($h = fopen($toFile, 'wb')) === false) {
            $error = 'Can\'t open file ' . $toFile;
            return false;
        }

        fclose($h);

        $copyResult = @copy($url, $toFile);

        if (!$copyResult) {
            $errors= error_get_last();
            $error = 'COPY ERROR: ' . $errors['type'] . ' MSG: ' . $errors['message'];

            return false;
        }

        return true;
    }


    public function uploadFile($path): bool
    {
        if (empty($path)) {
            $absPath = $this->root;
        } else {
            $absPath = $this->root . '/' . $path;
        }

        $result = false;

        if (!empty($_FILES) && !empty($_FILES['file']['name'])) {
            $newfile = $_FILES['file']['name'];
            $absPath .= '/' . $newfile;
            move_uploaded_file($_FILES['file']['tmp_name'], $absPath);
            $result = true;
        }

        return $result;
    }

    public function getFilePerms($file): array
    {   
        $result = [];
        $rwx = ['---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx'];
        
        $file = $this->root . '/' . $file;

        if (
            (is_dir($file) || file_exists($file))
            && (function_exists('posix_getpwuid') && function_exists('posix_getgrgid'))
        ) {
            $perms = substr(sprintf('%o', fileperms($file)), -4);
            $result[] = $perms;
            $type = is_dir($file) ? 'd' : '-';
            $result[] = $type . $rwx[$perms[1]] . $rwx[$perms[2]] .$rwx[$perms[3]];
            $stat = stat($file);
            $pw = posix_getpwuid($stat[4]);
            $gr = posix_getgrgid($stat[5]);
            $result[] = $pw['name'] . ':' . $gr['name'];
        }

        if (empty($result)) {
            $result = ['', '', ''];
        }

        return $result;
    }

    public function getDateModified($file): string
    {
        $file = $this->root . '/' . $file;

        return date ('Y-m-d H:i:s', filemtime($file));
    }

    public function getSize($file, $precision = 2): string 
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $file = $this->root . '/' . $file;

        $bytes = filesize($file);
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}


class Zip extends ZipArchive 
{ 
    public function addDir($path, $withoutRootPath = ''): bool 
    {  
        $path = rtrim($path, '/');
        $path  = realpath($path);

        if (!is_dir($path)) {
            return false;
        }

        $nodes = glob($path . '/*'); 

        foreach ($nodes as $node) { 
            if (is_dir($node)) { 
                $this->addDir($node, $withoutRootPath); 
            } elseif (is_file($node))  {
                $entryName = '';
                
                if (!empty($withoutRootPath)) {
                    $entryName = str_replace($withoutRootPath, '', $node);
                }
                
                $this->addFile($node, $entryName); 
            } 
        }

        return true; 
    } 
} 

class Mime
{
    /**
     * Mime types array.
     *
     * @var array
     */
    protected static $types = [
        '3dm' => 'x-world/x-3dmf',
        '3dmf' => 'x-world/x-3dmf',
        'a' => 'application/octet-stream',
        'aab' => 'application/x-authorware-bin',
        'aam' => 'application/x-authorware-map',
        'aas' => 'application/x-authorware-seg',
        'abc' => 'text/vnd.abc',
        'acgi' => 'text/html',
        'afl' => 'video/animaflex',
        'ai' => 'application/postscript',
        'aif' => 'audio/aiff',
        'aifc' => 'audio/aiff',
        'aiff' => 'audio/aiff',
        'aim' => 'application/x-aim',
        'aip' => 'text/x-audiosoft-intra',
        'ani' => 'application/x-navi-animation',
        'aos' => 'application/x-nokia-9000-communicator-add-on-software',
        'aps' => 'application/mime',
        'arc' => 'application/octet-stream',
        'arj' => 'application/arj',
        'art' => 'image/x-jg',
        'asf' => 'video/x-ms-asf',
        'asm' => 'text/x-asm',
        'asp' => 'text/asp',
        'asx' => 'application/x-mplayer2',
        'au' => 'audio/basic',
        'avi' => 'video/avi',
        'avs' => 'video/avs-video',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bm' => 'image/bmp',
        'bmp' => 'image/bmp',
        'boo' => 'application/book',
        'book' => 'application/book',
        'boz' => 'application/x-bzip2',
        'bsh' => 'application/x-bsh',
        'bz' => 'application/x-bzip',
        'bz2' => 'application/x-bzip2',
        'c' => 'text/plain',
        'c++' => 'text/plain',
        'cat' => 'application/vnd.ms-pki.seccat',
        'cc' => 'text/plain',
        'ccad' => 'application/clariscad',
        'cco' => 'application/x-cocoa',
        'cdf' => 'application/cdf',
        'cer' => 'application/pkix-cert',
        'cha' => 'application/x-chat',
        'chat' => 'application/x-chat',
        'class' => 'application/java',
        'com' => 'application/octet-stream',
        'conf' => 'text/plain',
        'cpio' => 'application/x-cpio',
        'cpp' => 'text/x-c',
        'cpt' => 'application/x-cpt',
        'crl' => 'application/pkcs-crl',
        'css' => 'text/css',
        'def' => 'text/plain',
        'der' => 'application/x-x509-ca-cert',
        'dif' => 'video/x-dv',
        'dir' => 'application/x-director',
        'dl' => 'video/dl',
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'dp' => 'application/commonground',
        'drw' => 'application/drafting',
        'dump' => 'application/octet-stream',
        'dv' => 'video/x-dv',
        'dvi' => 'application/x-dvi',
        'dwf' => 'drawing/x-dwf (old,',
        'dwg' => 'application/acad',
        'dxf' => 'application/dxf',
        'eps' => 'application/postscript',
        'es' => 'application/x-esrehber',
        'etx' => 'text/x-setext',
        'evy' => 'application/envoy',
        'exe' => 'application/octet-stream',
        'f' => 'text/plain',
        'f90' => 'text/x-fortran',
        'fdf' => 'application/vnd.fdf',
        'fif' => 'image/fif',
        'fli' => 'video/fli',
        'flv' => 'video/x-flv',
        'for' => 'text/x-fortran',
        'fpx' => 'image/vnd.fpx',
        'g' => 'text/plain',
        'g3' => 'image/g3fax',
        'gif' => 'image/gif',
        'gl' => 'video/gl',
        'gsd' => 'audio/x-gsm',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-compressed',
        'h' => 'text/plain',
        'help' => 'application/x-helpfile',
        'hgl' => 'application/vnd.hp-hpgl',
        'hh' => 'text/plain',
        'hlp' => 'application/x-winhelp',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htmls' => 'text/html',
        'htt' => 'text/webviewhtml',
        'htx' => 'text/html',
        'ice' => 'x-conference/x-cooltalk',
        'ico' => 'image/x-icon',
        'idc' => 'text/plain',
        'ief' => 'image/ief',
        'iefs' => 'image/ief',
        'iges' => 'application/iges',
        'igs' => 'application/iges',
        'ima' => 'application/x-ima',
        'imap' => 'application/x-httpd-imap',
        'inf' => 'application/inf',
        'ins' => 'application/x-internett-signup',
        'ip' => 'application/x-ip2',
        'isu' => 'video/x-isvideo',
        'it' => 'audio/it',
        'iv' => 'application/x-inventor',
        'ivr' => 'i-world/i-vrml',
        'ivy' => 'application/x-livescreen',
        'jam' => 'audio/x-jam',
        'jav' => 'text/plain',
        'java' => 'text/plain',
        'jcm' => 'application/x-java-commerce',
        'jfif' => 'image/jpeg',
        'jfif-tbnl' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jps' => 'image/x-jps',
        'js' => 'application/x-javascript',
        'jut' => 'image/jutvision',
        'kar' => 'audio/midi',
        'ksh' => 'application/x-ksh',
        'la' => 'audio/nspaudio',
        'lam' => 'audio/x-liveaudio',
        'latex' => 'application/x-latex',
        'lha' => 'application/lha',
        'lhx' => 'application/octet-stream',
        'list' => 'text/plain',
        'lma' => 'audio/nspaudio',
        'log' => 'text/plain',
        'lsp' => 'application/x-lisp',
        'lst' => 'text/plain',
        'lsx' => 'text/x-la-asf',
        'ltx' => 'application/x-latex',
        'lzh' => 'application/octet-stream',
        'lzx' => 'application/lzx',
        'm' => 'text/plain',
        'm1v' => 'video/mpeg',
        'm2a' => 'audio/mpeg',
        'm2v' => 'video/mpeg',
        'm3u' => 'audio/x-mpequrl',
        'man' => 'application/x-troff-man',
        'map' => 'application/x-navimap',
        'mar' => 'text/plain',
        'mbd' => 'application/mbedlet',
        'mc$' => 'application/x-magic-cap-package-1.0',
        'mcd' => 'application/mcad',
        'mcf' => 'image/vasa',
        'mcp' => 'application/netmc',
        'me' => 'application/x-troff-me',
        'mht' => 'message/rfc822',
        'mhtml' => 'message/rfc822',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mif' => 'application/x-frame',
        'mime' => 'message/rfc822',
        'mjf' => 'audio/x-vnd.audioexplosion.mjuicemediafile',
        'mjpg' => 'video/x-motion-jpeg',
        'mm' => 'application/base64',
        'mme' => 'application/base64',
        'mod' => 'audio/mod',
        'moov' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg3',
        'mpa' => 'audio/mpeg',
        'mpc' => 'application/x-project',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'mpp' => 'application/vnd.ms-project',
        'mpt' => 'application/x-project',
        'mpv' => 'application/x-project',
        'mpx' => 'application/x-project',
        'mrc' => 'application/marc',
        'ms' => 'application/x-troff-ms',
        'mv' => 'video/x-sgi-movie',
        'my' => 'audio/make',
        'mzz' => 'application/x-vnd.audioexplosion.mzz',
        'nap' => 'image/naplps',
        'naplps' => 'image/naplps',
        'nc' => 'application/x-netcdf',
        'ncm' => 'application/vnd.nokia.configuration-message',
        'nif' => 'image/x-niff',
        'niff' => 'image/x-niff',
        'nix' => 'application/x-mix-transfer',
        'nsc' => 'application/x-conference',
        'nvd' => 'application/x-navidoc',
        'o' => 'application/octet-stream',
        'oda' => 'application/oda',
        'omc' => 'application/x-omc',
        'omcd' => 'application/x-omcdatamaker',
        'omcr' => 'application/x-omcregerator',
        'p' => 'text/x-pascal',
        'p10' => 'application/pkcs10',
        'p12' => 'application/pkcs-12',
        'p7a' => 'application/x-pkcs7-signature',
        'p7c' => 'application/pkcs7-mime',
        'pas' => 'text/pascal',
        'pbm' => 'image/x-portable-bitmap',
        'pcl' => 'application/vnd.hp-pcl',
        'pct' => 'image/x-pict',
        'pcx' => 'image/x-pcx',
        'pdf' => 'application/pdf',
        'pfunk' => 'audio/make',
        'pgm' => 'image/x-portable-graymap',
        'pic' => 'image/pict',
        'pict' => 'image/pict',
        'pkg' => 'application/x-newton-compatible-pkg',
        'pko' => 'application/vnd.ms-pki.pko',
        'pl' => 'text/plain',
        'plx' => 'application/x-pixclscript',
        'pm' => 'image/x-xpixmap',
        'png' => 'image/png',
        'pnm' => 'application/x-portable-anymap',
        'pot' => 'application/mspowerpoint',
        'pov' => 'model/x-pov',
        'ppa' => 'application/vnd.ms-powerpoint',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/mspowerpoint',
        'ppt' => 'application/mspowerpoint',
        'ppz' => 'application/mspowerpoint',
        'pre' => 'application/x-freelance',
        'prt' => 'application/pro_eng',
        'ps' => 'application/postscript',
        'psd' => 'application/octet-stream',
        'pvu' => 'paleovu/x-pv',
        'pwz' => 'application/vnd.ms-powerpoint',
        'py' => 'text/x-script.phyton',
        'pyc' => 'applicaiton/x-bytecode.python',
        'qcp' => 'audio/vnd.qcelp',
        'qd3' => 'x-world/x-3dmf',
        'qd3d' => 'x-world/x-3dmf',
        'qif' => 'image/x-quicktime',
        'qt' => 'video/quicktime',
        'qtc' => 'video/x-qtc',
        'qti' => 'image/x-quicktime',
        'qtif' => 'image/x-quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'application/x-cmu-raster',
        'rast' => 'image/cmu-raster',
        'rexx' => 'text/x-script.rexx',
        'rf' => 'image/vnd.rn-realflash',
        'rgb' => 'image/x-rgb',
        'rm' => 'application/vnd.rn-realmedia',
        'rmi' => 'audio/mid',
        'rmm' => 'audio/x-pn-realaudio',
        'rmp' => 'audio/x-pn-realaudio',
        'rng' => 'application/ringing-tones',
        'rnx' => 'application/vnd.rn-realplayer',
        'roff' => 'application/x-troff',
        'rp' => 'image/vnd.rn-realpix',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'rt' => 'text/richtext',
        'rtf' => 'text/richtext',
        'rtx' => 'application/rtf',
        'rv' => 'video/vnd.rn-realvideo',
        's' => 'text/x-asm',
        's3m' => 'audio/s3m',
        'saveme' => 'application/octet-stream',
        'sbk' => 'application/x-tbook',
        'scm' => 'application/x-lotusscreencam',
        'sdml' => 'text/plain',
        'sdp' => 'application/sdp',
        'sdr' => 'application/sounder',
        'sea' => 'application/sea',
        'set' => 'application/set',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'sh' => 'application/x-bsh',
        'shtml' => 'text/html',
        'sid' => 'audio/x-psid',
        'sit' => 'application/x-sit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'sl' => 'application/x-seelogo',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'snd' => 'audio/basic',
        'sol' => 'application/solids',
        'spc' => 'application/x-pkcs7-certificates',
        'spl' => 'application/futuresplash',
        'spr' => 'application/x-sprite',
        'sprite' => 'application/x-sprite',
        'src' => 'application/x-wais-source',
        'ssi' => 'text/x-server-parsed-html',
        'ssm' => 'application/streamingmedia',
        'sst' => 'application/vnd.ms-pki.certstore',
        'step' => 'application/step',
        'stl' => 'application/sla',
        'stp' => 'application/step',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'svf' => 'image/vnd.dwg',
        'svr' => 'application/x-world',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'talk' => 'text/x-speech',
        'tar' => 'application/x-tar',
        'tbk' => 'application/toolbook',
        'tcl' => 'application/x-tcl',
        'tcsh' => 'text/x-script.tcsh',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'text' => 'text/plain',
        'tgz' => 'application/x-compressed',
        'tif' => 'image/tiff',
        'tr' => 'application/x-troff',
        'tsi' => 'audio/tsp-audio',
        'tsp' => 'audio/tsplayer',
        'tsv' => 'text/tab-separated-values',
        'turbot' => 'image/florian',
        'txt' => 'text/plain',
        'uil' => 'text/x-uil',
        'uni' => 'text/uri-list',
        'unis' => 'text/uri-list',
        'unv' => 'application/i-deas',
        'uri' => 'text/uri-list',
        'uris' => 'text/uri-list',
        'ustar' => 'application/x-ustar',
        'uu' => 'application/octet-stream',
        'vcd' => 'application/x-cdlink',
        'vcs' => 'text/x-vcalendar',
        'vda' => 'application/vda',
        'vdo' => 'video/vdo',
        'vew' => 'application/groupwise',
        'viv' => 'video/vivo',
        'vivo' => 'video/vivo',
        'vmd' => 'application/vocaltec-media-desc',
        'vmf' => 'application/vocaltec-media-file',
        'voc' => 'audio/voc',
        'vos' => 'video/vosaic',
        'vox' => 'audio/voxware',
        'vqe' => 'audio/x-twinvq-plugin',
        'vqf' => 'audio/x-twinvq',
        'vql' => 'audio/x-twinvq-plugin',
        'vrml' => 'application/x-vrml',
        'vrt' => 'x-world/x-vrt',
        'vsd' => 'application/x-visio',
        'vst' => 'application/x-visio',
        'vsw' => 'application/x-visio',
        'w60' => 'application/wordperfect6.0',
        'w61' => 'application/wordperfect6.1',
        'w6w' => 'application/msword',
        'wav' => 'audio/wav',
        'wb1' => 'application/x-qpro',
        'wbmp' => 'image/vnd.wap.wbmp',
        'web' => 'application/vnd.xara',
        'wiz' => 'application/msword',
        'wk1' => 'application/x-123',
        'wmf' => 'windows/metafile',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'word' => 'application/msword',
        'wp' => 'application/wordperfect',
        'wp5' => 'application/wordperfect',
        'wp6' => 'application/wordperfect',
        'wpd' => 'application/wordperfect',
        'wq1' => 'application/x-lotus',
        'wri' => 'application/mswrite',
        'wrl' => 'application/x-world',
        'wrz' => 'model/vrml',
        'wsc' => 'text/scriplet',
        'wsrc' => 'application/x-wais-source',
        'wtk' => 'application/x-wintalk',
        'xbm' => 'image/x-xbitmap',
        'xdr' => 'video/x-amt-demorun',
        'xgz' => 'xgl/drawing',
        'xif' => 'image/vnd.xiff',
        'xl' => 'application/excel',
        'xla' => 'application/excel',
        'xlb' => 'application/excel',
        'xlc' => 'application/excel',
        'xld' => 'application/excel',
        'xlk' => 'application/excel',
        'xll' => 'application/excel',
        'xlm' => 'application/excel',
        'xls' => 'application/excel',
        'xlt' => 'application/excel',
        'xlv' => 'application/excel',
        'xlw' => 'application/excel',
        'xm' => 'audio/xm',
        'xml' => 'text/xml',
        'xmz' => 'xgl/movie',
        'xpix' => 'application/x-vnd.ls-xpix',
        'xpm' => 'image/x-xpixmap',
        'x-png' => 'image/png',
        'xsr' => 'video/x-amt-showrun',
        'xwd' => 'image/x-xwd',
        'xyz' => 'chemical/x-pdb',
        'z' => 'application/x-compress',
        'zip' => 'application/x-compressed',
        'zoo' => 'application/octet-stream',
        'zsh' => 'text/x-script.zsh',
    ];

    /**
     * getType
     * Gets corresponding mime type for the target file.
     *
     * @param string $file it can be absolute path to the file or simple file name
     * @param string $defaultType if mime type not found default one will be returned
     *
     * @return string
     */
    public static function getType($file, $defaultType = 'html')
    {
        $file = basename($file);
        $fileext = substr(strrchr($file, '.'), 1);

        if (empty($fileext)) {
            return isset(self::$types[$defaultType]) 
                ? self::$types[$defaultType] 
                : false;
        }

        return isset(self::$types[$fileext]) 
            ? self::$types[$fileext] 
            : self::$types[$defaultType];
    }
}

function getRequestParam($name, $defaultValue = null) 
{
    if (array_key_exists($name, $_REQUEST)) {
        $result = $_REQUEST[$name];
    } else {
        $result = $defaultValue;
    }

    return $result;
}

$entryPointManager = (new EntryPointManager(new FileManager($_SERVER['DOCUMENT_ROOT'])))->handleRequest();
