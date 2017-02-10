<?php
/*
Plugin Name: Happyr employer
Plugin URI: 
Description: Show jobs for a Happyr epmployer
Author: Tobias Nyholm
Version: 1.0
Author URI: https://happyr.com
*/
/*
Copyright (C) Tobias Nyholm

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * A small wordpress plugin for Happyr employers
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HappyrEmployer {

    /**
     * Get the URL for subscription.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function subscription($attributes)
    {
        $attributes = shortcode_atts(array('id' => ''), $attributes, 'happyrsubscription');

        $id = $attributes['id'];
        if (empty($id)) {
            return 'You are missing company ID. See https://happyr.com/integration/doc/retrieveId';
        }

        return 'https://happyr.com/user/spontaneous/'.$id.'/start';
    }

    /**
     * Show all available jobs. You can group by 'city', 'name' and 'location_name'.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function jobs($attributes)
    {
        $attributes = shortcode_atts(array('id' => '', 'groupby'=>'city'), $attributes, 'happyrjobs');

        $id = $attributes['id'];
        if (empty($id)) {
            return 'You are missing company ID. See https://happyr.com/integration/doc/retrieveId';
        }

        $url = 'https://happyr.com/integration/v1/company/'.$id.'/adverts?_format=json';
        $content = file_get_contents($url);
        $data = json_decode($content, true);

        $groupBy = $attributes['groupby'];
        $group = array();

        switch ($groupBy) {
            case 'name':
                $link = 'location_name';
                break;
            default:
                $link = 'name';
        }

        foreach ($data as $advert) {
            $group[$advert[$groupBy]][] = $advert;
        }

        $content = '';
        foreach ($group as $heading => $adverts) {
            $content.='<h2>'.$heading.'</h2>';
            foreach ($adverts as $advert) {
                $content.='<a href="'.$advert['url'].'">'.$advert[$link].'</a> | '.$advert['address'].'<br>';
            }
        }
        return $content;
    }
}

add_shortcode('happyrsubscription', array('HappyrEmployer', 'subscription'));
add_shortcode('happyrjobs', array('HappyrEmployer', 'jobs'));
