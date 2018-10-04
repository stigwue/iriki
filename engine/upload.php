<?php
/*
This file uploader has a three part aim:
1. Put files in a yyyy/mm year-month structure so as not to max OS maximum file per directory limits.
2. Make files accessible via http
3. Make files accessible via the file/directory paths

In essense:
Files exist in year/month/ directories
Which can then be referred to in two ways;
via the file system e.g /path/on/server/files/user_uploads/2017/11/avatar.png
via http: http://address_via_http_path/files/user_uploads/2017/11/avatar.png

This was initially written to use php variables like DIRECTORY_SEPARATOR and __DIR__.
But we've come very paranoid of late (after watching the movie IT?), eschewing (great chance to use this word) variables!

By the way, all directory paths end with '/'. Go figure!

Authors: Isah Lata (github.com/latanoel) and Stephen Igwue(github.com/stigwue).
*/

//date() might throw a warning about timezones, safe to use Lay-gos
//date_default_timezone_set('Africa/Lagos');
//commmented out as Iriki would already declare a time zone

namespace iriki;

class upload extends \iriki\engine\request
{
    public static function get_upload_dir()
    {
        return (isset($GLOBALS['APP']['config']['constants']['upload_dir']) ? $GLOBALS['APP']['config']['constants']['upload_dir'] : null);
    }

    public static function get_upload_http()
    {
        return (isset($GLOBALS['APP']['config']['constants']['upload_http']) ? $GLOBALS['APP']['config']['constants']['upload_http'] : null);
    }

    public function path($request, $wrap = true)
    {
        $data = $request->getData();
        if (\iriki\engine\type::is_type($data['timestamp'], 'number'))
        {
            $timestamp = $data['timestamp'];
            //files are shelved in {{upload_dir}}/yyyy/mm/file_name.ext
            $path = '';
            $shelf = date('Y/m/', $timestamp); //->yyyy/mm/

            $upload_dir = Self::get_upload_dir();
            if (!is_null($upload_dir))
            {
                $path = array(
                    'relative' => $shelf,
                    'absolute' => $upload_dir . $shelf //absolute (full) path in thine server
                );

                $status = true;

                if (!file_exists($path['absolute']))
                {
                    $status = mkdir($path['absolute'], 0777, true);
                }

                return \iriki\engine\response::information($path, $wrap, $status);
            }
            else
            {
                //upload_dir not set in constants
                return \iriki\engine\response::error('Upload directory value (upload_dir) not set in constants.', $wrap);
            }
        }
        else
        {
            //timestamp is not a number
            return \iriki\engine\response::error('Supplied timestamp is not a number.', $wrap);
        }
    }

    public function http_base($request, $wrap = true)
    {
        $base_path = Self::get_upload_http();
            
        if (!is_null($base_path))
        {
            {
                //upload_dir not set in constants
                return \iriki\engine\response::information($base_path, $wrap);
            }
        }
        else
        {
            //timestamp is not a number
            return \iriki\engine\response::error('Upload_http constant not set.', $wrap);
        }
    }

    public function upload($request, $wrap = true)
    {
        $data = $request->getData();
        
        //uploaded file location on server
        $temp_path = (isset($data['file']['tmp_name']) ? $data['file']['tmp_name'] : null);

        if (is_null($temp_path) || !file_exists($temp_path))
        {
            return \iriki\engine\response::error('No file was uploaded.', $wrap);
        }
        else
        {
            //get the path
            $new_request = new upload(); //$request;
            //set new request timestamp
            $new_request->setData([
                'timestamp' => time(NULL)
            ]);
            $new_request->setParameterStatus(array(
                'final' => array('timestamp'),
                'missing' => array(),
                'extra' => array(),
                'ids' => array()
            ));

            //make request
            $path = $new_request->path($new_request, true);

            //test existence
            if ($path['code'] == 200 AND $path['data'] == true)
            {
                //use $path['message']
                $upload_dir = $path['message']['absolute'];

                //check for title
                $title = $data['title'];

                if ($title == '')
                {
                    $title = $data['file']['name'];
                }

                $destination_path = $upload_dir . $title;
                $file_path = $path['message']['relative'] . $title;

                //check if overwrite is true
                $do_overwrite = $data['overwrite'];
                if (\iriki\engine\type::is_type($do_overwrite, 'boolean'))
                {
                    $do_overwrite = \iriki\engine\type::ctype($do_overwrite, 'boolean');
                    
                    if (!$do_overwrite)
                    {
                        //check to see it doesn't already exist
                        if (file_exists($destination_path))
                        {
                            return \iriki\engine\response::error("File already exists.", $wrap);
                        }
                    }
                }
                else
                {
                    return \iriki\engine\response::error("Supplied 'overwrite' value is not boolean.", $wrap);
                }

                if (move_uploaded_file($temp_path, $destination_path))
                {
                    //build upload object
                    $upload = array(
                        'title' => $title,
                        'path' => $file_path,
                        'tag' => $data['tag']
                    );

                    $request->setData($upload);
                    $request->setParameterStatus([
                        'final' => array('title', 'path', 'tag'),
                        'missing' => array(),
                        'extra' => array(),
                        'ids' => array()
                    ]);

                    $result = $request->create($request, $wrap);

                    return $result;
                }
                else
                {
                    return \iriki\engine\response::error('Some error occurred while uploading file.', $wrap);
                }
            }
            else
            {
                return \iriki\engine\response::error('Some error occurred while preparing upload path.', $wrap);
            }
        }
    }

}
?>
