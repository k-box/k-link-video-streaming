<?php


if (! function_exists('mix_url')) {
    /**
     * Generate the URL of a Mix managed asset by taking into 
     * consideration the deployment style.
     *
     * @return string the asset absolute URL
     */
    function mix_url($asset)
    {
        $sub_folder = config('deployment.sub_folder');

        if(!empty($sub_folder)){
            return url($sub_folder .'/'. (string)mix($asset));
        }

        return url((string)mix($asset));
    }
}