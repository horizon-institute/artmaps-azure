<?php
require_once("../../../../wp-config.php");
return;
/*$users = array(
array("giannachi", "apple", "http://wp-artmaps.cloudapp.net/wordpress/giannachi", "g.giannachi@exeter.ac.uk"),
array("jessicak", "banana", "http://wp-artmaps.cloudapp.net/wordpress/jessicak", "jessica.king@tate.org.uk"),
array("laura", "bilberry", "http://wp-artmaps.cloudapp.net/wordpress/laura", "laura.carletti@nottingham.ac.uk"),
array("liam", "clementine", "http://wp-artmaps.cloudapp.net/wordpress/liam", "liam.palmer@ymail.com"),
array("liambenpalmer", "damson", "http://wp-artmaps.cloudapp.net/wordpress/liambenpalmer", "liambenpalmer@gmail.com"),
array("nk204", "dragonfruit", "http://wp-artmaps.cloudapp.net/wordpress/nk204", "n.kaye@exeter.ac.uk"),
array("helengriffiths", "grape", "http://wp-artmaps.cloudapp.net/wordpress/helengriffiths", "helen.sian.griffiths@googlemail.com"),
array("kirstiebeaven", "huckleberry", "http://wp-artmaps.cloudapp.net/wordpress/kirstiebeaven", "kirstiebeaven@gmail.com"),
array("rosiecardiff", "jambul", "http://wp-artmaps.cloudapp.net/wordpress/rosiecardiff", "rosiecardiff@gmail.com"),
array("alexpilcher", "lemon", "http://wp-artmaps.cloudapp.net/wordpress/alexpilcher", "alex.h.pilcher@gmail.com"),
array("stevenbenford", "lychee", "http://wp-artmaps.cloudapp.net/wordpress/stevenbenford", "steven.benford@gmail.com"),
array("johnfstack", "melon", "http://wp-artmaps.cloudapp.net/wordpress/johnfstack", "johnfstack@gmail.com"),
array("elenavillaespesa", "plum", "http://wp-artmaps.cloudapp.net/wordpress/elenavillaespesa", "elenavillaespesa@gmail.com"),
array("rebeccasinker", "redcurrant", "http://wp-artmaps.cloudapp.net/wordpress/rebeccasinker", "rebecca.sinker@gmail.com"),
array("derekmcauley", "satsuma", "http://wp-artmaps.cloudapp.net/wordpress/derekmcauley", "derek.mcauley@gmail.com"),
array("hannah", "parsnip", "http://wp-artmaps.cloudapp.net/wordpress/hannah", "hannahmarywhite@yahoo.co.uk")
);*/

/*$users = array(
        array("picasso", "lily", "http://wp-artmaps.cloudapp.net/wordpress/picasso", "picasso@artmaps.org.uk"),
        array("hirst", "daffodil", "http://wp-artmaps.cloudapp.net/wordpress/hirst", "hirst@artmaps.org.uk"),
        array("waterhouse", "foxglove", "http://wp-artmaps.cloudapp.net/wordpress/waterhouse", "waterhouse@artmaps.org.uk"),
        array("lichtenstein", "hyacinth", "http://wp-artmaps.cloudapp.net/wordpress/lichtenstein", "lichtenstein@artmaps.org.uk"),
        array("matisse", "lilac", "http://wp-artmaps.cloudapp.net/wordpress/matisse", "matisse@artmaps.org.uk"),
        array("warhol", "marigold", "http://wp-artmaps.cloudapp.net/wordpress/warhol", "warhol@artmaps.org.uk"),
        array("dali", "thistle", "http://wp-artmaps.cloudapp.net/wordpress/dali", "dali@artmaps.org.uk"),
        array("turner", "orchid", "http://wp-artmaps.cloudapp.net/wordpress/turner", "turner@artmaps.org.uk"),
        array("munch", "snapdragon", "http://wp-artmaps.cloudapp.net/wordpress/munch", "munch@artmaps.org.uk"),
        array("hockney", "yarrow", "http://wp-artmaps.cloudapp.net/wordpress/hockney", "hockney@artmaps.org.uk")
);*/

$users = array(
);

foreach ($users as $user) {
    echo "$user[3]<br />";
    $u = get_user_by_email($user[3]);
    $cfg = getUsersArtMapsBlog($u);
    echo $cfg["InternalURL"] . "<br />";
    $cfg["IsInternal"] = true;
    $cfg["InternalURL"] = $user[2];
    $cfg["InternalUsername"] = $user[0];
    $cfg["InternalPassword"] = $user[1];
    update_user_meta($u->id, "artmaps_blog_config", $cfg);
}
?>
