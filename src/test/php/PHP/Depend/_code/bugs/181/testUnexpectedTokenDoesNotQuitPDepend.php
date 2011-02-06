<?php

// traditional chinese Language Module for v2.3 (translated by www.which.tw)
global $_VERSION;

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "錯誤",
	"back"			=> "回上頁",
	
	// root
	"home"			=> "主目錄並不存在, 請檢查設定.",
	"abovehome"		=> "目前的目錄可能沒有在主目錄上.",
	"targetabovehome"	=> "目標的目錄可能沒有在主目錄上.",
	
	// exist
	"direxist"		=> "此目錄不存在.",
	//"filedoesexist"	=> "此目錄已存在.",
	"fileexist"		=> "此檔案不存在.",
	"itemdoesexist"		=> "此項目已存在.",
	"itemexist"		=> "此項目不存在.",
	"targetexist"		=> "這目標目錄不存在.",
	"targetdoesexist"	=> "這目標項目已存在.",
	
	// open
	"opendir"		=> "無法打開目錄.",
	"readdir"		=> "無法讀取目錄.",
	
	// access
	"accessdir"		=> "您不允許存取這個目錄.",
	"accessfile"		=> "您不允許存取這個檔案.",
	"accessitem"		=> "您不允許存取這個項目.",
	"accessfunc"		=> "您不允許使用這個功能.",
	"accesstarget"		=> "您不允許存取這個目標目錄.",
	
	// actions
	"permread"		=> "取得權限失敗.",
	"permchange"		=> "權限更改失敗.",
	"openfile"		=> "打開檔案失敗.",
	"savefile"		=> "檔案儲存失敗.",
	"createfile"		=> "新增檔案失敗.",
	"createdir"		=> "新增目錄失敗.",
	"uploadfile"		=> "檔案上傳失敗.",
	"copyitem"		=> "複製失敗.",
	"moveitem"		=> "移動失敗.",
	"delitem"		=> "刪除失敗.",
	"chpass"		=> "更改密碼失敗.",
	"deluser"		=> "移除使用者失敗.",
	"adduser"		=> "加入使用者失敗.",
	"saveuser"		=> "儲存使用者失敗.",
	"searchnothing"		=> "您必須輸入些什麼來搜尋.",
	
	// misc
	"miscnofunc"		=> "功能無效.",
	"miscfilesize"		=> "檔案大小已達到最大.",
	"miscfilepart"		=> "檔案只有一部分上傳.",
	"miscnoname"		=> "您必須輸入名稱.",
	"miscselitems"		=> "您還未選擇任何項目.",
	"miscdelitems"		=> "您確定要刪除這些 {0} 項目?",
	"miscdeluser"		=> "您確定要刪除使用者 '{0}'?",
	"miscnopassdiff"	=> "新密碼跟舊密碼相同.",
	"miscnopassmatch"	=> "密碼不符.",
	"miscfieldmissed"	=> "您遺漏一個重要欄位.",
	"miscnouserpass"	=> "使用者名稱或密碼錯誤.",
	"miscselfremove"	=> "您無法移除您自己.",
	"miscuserexist"		=> "使用者已存在.",
	"miscnofinduser"	=> "無法找到使用者.",
	"extract_noarchive" => "此檔案無法執行壓縮.",
	"extract_unknowntype" => "未知的壓縮類型"	,
	
	'chmod_none_not_allowed' => 'Changing Permissions to <none> is not allowed',
	'archive_dir_notexists' => 'The Save-To Directory you have specified does not exist.',
	'archive_dir_unwritable' => 'Please specify a writable directory to save the archive to.',
	'archive_creation_failed' => 'Failed saving the Archive File'
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "更改權限",
	"editlink"		=> "編輯",
	"downlink"		=> "下載",
	"uplink"		=> "上一層",
	"homelink"		=> "主頁",
	"reloadlink"		=> "重新載入",
	"copylink"		=> "複製",
	"movelink"		=> "移動",
	"dellink"		=> "刪除",
	"comprlink"		=> "壓縮",
	"adminlink"		=> "管理員",
	"logoutlink"		=> "登出",
	"uploadlink"		=> "上傳",
	"searchlink"		=> "搜尋",
	"extractlink"	=> "解開壓縮檔",
	'chmodlink'		=> '更改 (chmod) 權限 (Folder/File(s))', // new mic
	'mossysinfolink'	=> 'eXtplorer 系統資訊 (eXtplorer, Server, PHP, mySQL)', // new mic
	'logolink'		=> '前往 joomlaXplorer 網站 (另開視窗)', // new mic
	
	// list
	"nameheader"		=> "名稱",
	"sizeheader"		=> "大小",
	"typeheader"		=> "類型",
	"modifheader"		=> "最後更新",
	"permheader"		=> "權限",
	"actionheader"		=> "動作",
	"pathheader"		=> "路徑",
	
	// buttons
	"btncancel"		=> "取消",
	"btnsave"		=> "儲存",
	"btnchange"		=> "更改",
	"btnreset"		=> "重設",
	"btnclose"		=> "關閉",
	"btncreate"		=> "新增",
	"btnsearch"		=> "搜尋",
	"btnupload"		=> "上傳",
	"btncopy"		=> "複製",
	"btnmove"		=> "移動",
	"btnlogin"		=> "登入",
	"btnlogout"		=> "登出",
	"btnadd"		=> "增加",
	"btnedit"		=> "編輯",
	"btnremove"		=> "移除",
	
		// user messages, new in joomlaXplorer 1.3.0
	"renamelink"	=> "重新命名",
	"confirm_delete_file" => "您確定要刪除這個檔案? \\n%s",
	"success_delete_file" => "物件成功刪除.",
	"success_rename_file" => "此目錄/檔案 %s 已成功重新命名為 %s.",
	
// actions
	"actdir"		=> "目錄",
	"actperms"		=> "更改權限",
	"actedit"		=> "編輯檔案",
	"actsearchresults"	=> "搜尋結果",
	"actcopyitems"		=> "複製項目",
	"actcopyfrom"		=> "從 /%s 複製到 /%s ",
	"actmoveitems"		=> "移動項目",
	"actmovefrom"		=> "從 /%s 移動到 /%s ",
	"actlogin"		=> "登入",
	"actloginheader"	=> "登入以使用 QuiXplorer",
	"actadmin"		=> "管理選單",
	"actchpwd"		=> "更改密碼",
	"actusers"		=> "使用者",
	"actarchive"		=> "壓縮項目",
	"actupload"		=> "上傳檔案",
	
	// misc
	"miscitems"		=> "項目",
	"miscfree"		=> "Free",
	"miscusername"		=> "使用者名稱",
	"miscpassword"		=> "密碼",
	"miscoldpass"		=> "舊密碼",
	"miscnewpass"		=> "新密碼",
	"miscconfpass"		=> "確認密碼",
	"miscconfnewpass"	=> "確認新密碼",
	"miscchpass"		=> "更改密碼",
	"mischomedir"		=> "主頁目錄",
	"mischomeurl"		=> "主頁 URL",
	"miscshowhidden"	=> "顯示隱藏項目",
	"mischidepattern"	=> "隱藏樣式",
	"miscperms"		=> "權限",
	"miscuseritems"		=> "(名稱, 主頁目錄, 顯示隱藏項目, 權限, 啟用)",
	"miscadduser"		=> "增加使用者",
	"miscedituser"		=> "編輯使用者 '%s'",
	"miscactive"		=> "啟用",
	"misclang"		=> "語言",
	"miscnoresult"		=> "無結果可用.",
	"miscsubdirs"		=> "搜尋子目錄",
	"miscpermnames"		=> array("只能瀏覽","修改","更改密碼","修改及更改密碼",
					"管理員"),
	"miscyesno"		=> array("是的","否","Y","N"),
	"miscchmod"		=> array("擁有者", "群組", "公開的"),
	
	// from here all new by mic
	"miscowner"			=> "擁有者",
	"miscownerdesc"		=> "<strong>描述:</strong><br />使用者 (UID) /<br />群組 (GID)<br />目前權限:<br /><strong> %s ( %s ) </strong>/<br /><strong> %s ( %s )</strong>",

	// sysinfo (new by mic)
	"simamsysinfo"		=> 'eXtplorer 系統資訊",
	"sisysteminfo"		=> "系統資訊",
	"sibuilton"			=> "運行系統",
	"sidbversion"		=> "資料庫版本 (MySQL)",
	"siphpversion"		=> "PHP 版本",
	"siphpupdate"		=> "INFORMATION: The PHP version you use is <strong>not</strong> actual!<br />To guarantee all functions and features of Mambo and addons,<br />you should use as minimum <strong>PHP.Version 4.3</strong>!",
	"siwebserver"		=> "Webserver",
	"siwebsphpif"		=> "網頁伺服器 - PHP 介面",
	'simamboversion'	=> 'eXtplorer 版本',
	"siuseragent"		=> "瀏覽器版本",
	"sirelevantsettings" => "重要的 PHP 設定",
	"sisafemode"		=> "安全模式",
	"sibasedir"			=> "Open basedir",
	"sidisplayerrors"	=> "PHP Errors",
	"sishortopentags"	=> "Short Open Tags",
	"sifileuploads"		=> "檔案上傳",
	"simagicquotes"		=> "Magic Quotes",
	"siregglobals"		=> "Register Globals",
	"sioutputbuf"		=> "Output Buffer",
	"sisesssavepath"	=> "Session Savepath",
	"sisessautostart"	=> "Session auto start",
	"sixmlenabled"		=> "XML 已啟動",
	"sizlibenabled"		=> "ZLIB 已啟動",
	"sidisabledfuncs"	=> "Non enabled functions",
	"sieditor"			=> "WYSIWYG 編輯器",
	"siconfigfile"		=> "Config file",
	"siphpinfo"			=> "PHP Info",
	"siphpinformation"	=> "PHP Information",
	"sipermissions"		=> "權限",
	"sidirperms"		=> "目錄權限",
	"sidirpermsmess"	=> "To be shure that all functions and features of eXtplorer are working correct, following folders should have permission to write [chmod 0777]",
	"sionoff"			=> array( "On", "Off" ),
	
	"extract_warning" => "您確定要在此處解壓檔案?\\n如果不小心使用這將會覆蓋已經存在的檔案!",
	"extract_success" => "解壓縮成功 ",
	"extract_failure" => "解壓縮失敗",	
	
	'overwrite_files' => '複蓋已存在的檔案?',
	"viewlink"		=> "檢視",
	"actview"		=> "顯示檔案來源",
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_chmod.php file
	'recurse_subdirs'	=> 'Recurse into subdirectories?',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to footer.php file
	'check_version'	=> 'Check for latest version',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_rename.php file
	'rename_file'	=>	'Rename a directory or file...',
	'newname'		=>	'New Name',
	
	// added by Paulino Michelazzo (paulino@michelazzo.com.br) to fun_edit.php file
	'returndir'	=>	'Return to directory after saving?',
	'line'		=> 	'Line',
	'column'	=>	'Column',
	'wordwrap'	=>	'Wordwrap: (IE only)',
	'copyfile'	=>	'Copy file into this filename',
	
	// Bookmarks
	'quick_jump' => 'Quick Jump To',
	'already_bookmarked' => 'This directory is already bookmarked',
	'bookmark_was_added' => 'This directory was added to the bookmark list.',
	'not_a_bookmark' => 'This directory is not a bookmark.',
	'bookmark_was_removed' => 'This directory was removed from the bookmark list.',
	'bookmarkfile_not_writable' => "Failed to %s the bookmark.\n The Bookmark File '%s' \nis not writable.",
	
	'lbl_add_bookmark' => 'Add this Directory as Bookmark',
	'lbl_remove_bookmark' => 'Remove this Directory from the Bookmark List',
	
	'enter_alias_name' => 'Please enter the alias name for this bookmark',
	
	'normal_compression' => 'normal compression',
	'good_compression' => 'good compression',
	'best_compression' => 'best compression',
	'no_compression' => 'no compression',
	
	'creating_archive' => 'Creating Archive File...',
	'processed_x_files' => 'Processed %s of %s Files',
	
	'ftp_header' => 'Local FTP Authentication',
	'ftp_login_lbl' => 'Please enter the login credentials for the FTP server',
	'ftp_login_name' => 'FTP User Name',
	'ftp_login_pass' => 'FTP Password',
	'ftp_hostname_port' => 'FTP Server Hostname and Port <br />(Port is optional)',
	'ftp_login_check' => 'Checking FTP connection...',
	'ftp_connection_failed' => "The FTP server could not be contacted. \nPlease check that the FTP server is running on your server.",
	'ftp_login_failed' => "The FTP login failed. Please check the username and password and try again.",
		
	'switch_file_mode' => 'Current mode: <strong>%s</strong>. You could switch to %s mode.',
	'symlink_target' => 'Target of the Symbolic Link',
	
	"permchange"		=> "CHMOD Success:",
	"savefile"		=> "The File was saved.",
	"moveitem"		=> "Moving succeeded.",
	"copyitem"		=> "Copying succeeded.",
	'archive_name' 	=> 'Name of the Archive File',
	'archive_saveToDir' 	=> 'Save the Archive in this directory',
	
	'editor_simple'	=> 'Simple Editor Mode',
	'editor_syntaxhighlight'	=> 'Syntax-Highlighted Mode',

	'newlink'	=> 'New File/Directory',
	'show_directories' => 'Show Directories',
	'actlogin_success' => 'Login successful!',
	'actlogin_failure' => 'Login failed, try again.',
	'directory_tree' => 'Directory Tree',
	'browsing_directory' => 'Browsing Directory',
	'filter_grid' => 'Filter',
	'paging_page' => 'Page',
	'paging_of_X' => 'of {0}',
	'paging_firstpage' => 'First Page',
	'paging_lastpage' => 'Last Page',
	'paging_nextpage' => 'Next Page',
	'paging_prevpage' => 'Previous Page',
	
	'paging_info' => 'Displaying Items {0} - {1} of {2}',
	'paging_noitems' => 'No items to display',
	'aboutlink' => 'About...',
	'password_warning_title' => 'Important - Change your Password!',
	'password_warning_text' => 'The user account you are logged in with (admin with password admin) corresponds to the default eXtplorer priviliged account. Your eXtplorer installation is open to intrusion and you should immediately fix this security hole!',
	'change_password_success' => 'Your Password has been changed!',
	'success' => 'Success',
	'failure' => 'Failure',
	'dialog_title' => 'Website Dialog',
	'upload_processing' => 'Processing Upload, please wait...',
	'upload_completed' => 'Upload successful!',
	'acttransfer' => 'Transfer from another Server',
	'transfer_processing' => 'Processing Server-to-Server Transfer, please wait...',
	'transfer_completed' => 'Transfer completed!',
	'max_file_size' => 'Maximum File Size',
	'max_post_size' => 'Maximum Upload Limit',
	'done' => 'Done.',
	'permissions_processing' => 'Applying Permissions, please wait...',
	'archive_created' => 'The Archive File has been created!',
	'save_processing' => 'Saving File...',
	'current_user' => 'This script currently runs with the permissions of the following user:',
	'your_version' => 'Your Version',
	'search_processing' => 'Searching, please wait...',
	'url_to_file' => 'URL of the File',
	'file' => 'File'
);
?>