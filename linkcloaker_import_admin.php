<?php   
	if (!empty($_POST['delete_link'])) {
		
		$id = $_POST['delete_link'];
		
		global $wpdb;
		$table_name = $wpdb->prefix . "affiliate_link_cloaker";
		$myrow = $wpdb->get_row( "SELECT * FROM $table_name" );
		$apiKey = $myrow->api_key;
		
		//Delete link
		wp_remote_get('http://www.affiliatedefense.com/api/v1/cloaks/delete/'.$apiKey.'/'.$id);

                echo '<div class="wrap"><div id="message" class="updated">';
                echo "Your cloaked link entry has been removed.";
                echo '</div></div>';
		
	}

    
	if (!empty($_POST['link_account'])) {

        //Connect to database
		global $wpdb;
		$table_name = $wpdb->prefix . "affiliate_link_cloaker";

		//Get new key
		$apiKeyFormData = $_POST["link_cloaking_api_key"];

		//Get existing key
		$myrow = $wpdb->get_row( "SELECT * FROM $table_name" );
		$apiKey = $myrow->api_key;

         //Update key
		$wpdb->update( $table_name , array( 'api_key' => $apiKeyFormData), array( 'api_key' => $apiKey ), array('%s'), array( '%s' ));

        echo '<div class="wrap"><div id="setting-error-settings_updated" class="updated settings-error">';
        echo "Your API Key has been updated.";
        echo '</div></div>';

		$apiKeyFormValue = $apiKeyFormData;


	}
	
	if($_POST['add_link'] == 'Y') {  
            global $wpdb;
            $table_name = $wpdb->prefix . "affiliate_link_cloaker";
            $myrow = $wpdb->get_row( "SELECT * FROM $table_name" );
            $apiKey = $myrow->api_key;

            $affiliateLink = $_POST["link_cloaking_affiliate_link"];
            $cloakedLink = $_POST["link_cloaking_cloaked_link"];

            if ($affiliateLink != 'http://' && $cloakedLink != 'http://') {

				//Add link
                $url = 'http://www.affiliatedefense.com/api/v1/cloaks/post/'.$apiKey;

                $data = 'affiliateLink='.$affiliateLink.'&cloakedLink='.$cloakedLink;
                $ch = curl_init( $url );
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec( $ch );
                curl_close($ch);

                echo '<div class="wrap"><div id="setting-error-settings_updated" class="updated settings-error">';
                echo "Your new cloaked link entry has been added.";
                echo '</div></div>';

            }
        }
	
        else {
        //Grab data for table
		global $wpdb;
		$table_name = $wpdb->prefix . "affiliate_link_cloaker";
		$myrow = $wpdb->get_row( "SELECT * FROM $table_name" );
		$apiKey = $myrow->api_key;
		//Get existing links
		$response = wp_remote_get('http://www.affiliatedefense.com/api/v1/cloaks/GET/'.$apiKey);
		$body = wp_remote_retrieve_body( $response);
		$externalApiKey = $myrow->external_api_key;
		$apiKeyFormValue = $myrow->api_key;
	}

$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'tab1';  
?> 

<div class="wrap">  
<h2>Link Cloaker for Affiliates</h2>

 <h2 class="nav-tab-wrapper">  
            <a href="?page=Link_Cloaking&tab=tab1" class="nav-tab <?php echo $active_tab == 'tab1' ? 'nav-tab-active' : ''; ?>">Instructions</a>  
            <a href="?page=Link_Cloaking&tab=tab2" class="nav-tab <?php echo $active_tab == 'tab2' ? 'nav-tab-active' : ''; ?>">Cloaked Links</a>  
            <a href="?page=Link_Cloaking&tab=tab3" class="nav-tab <?php echo $active_tab == 'tab3' ? 'nav-tab-active' : ''; ?>">Add Cloaked Link</a>  
            <a href="?page=Link_Cloaking&tab=tab4" class="nav-tab <?php echo $active_tab == 'tab4' ? 'nav-tab-active' : ''; ?>">Existing Account</a>  
        </h2> 

<?php if( $active_tab == 'tab1' ) {  ?>
<h2>Instructions</h2>

<h3>Cloaking Links</h3>
<p>The link cloaker script has been automatically added to your WordPress site.
To cloak your affiliate links, go to the Add Cloaked Link tab. From here, enter your affiliate link in its original form and then how you'd like the link to appear to your website visitors.
The links you've added will appear on the Cloaked Links tab. You have the option of deleting them from here.</p>

<h3>Link Existing Account</h3>
<p>If you've already added cloaked links from an existing AffiliateDefense account, you can import them from the Existing Account tab. Find your API key from your AffiliateDefense account and enter it here.</p>

<h3>Remote Links</h3>
<p>
Your cloaked links are saved remotely to AffiliateDefense. This makes it easy to manage and sync your cloaked links from here or on AffilateDefense. This also makes it easy to use the link cloaker on a non WordPress website and to restore links at a later time. To manage your links from AffiliateDefense, make note of your API key from the text field under the Existing Account. When creating an account on AffiliateDefense, you'll need this API key to keep your links in sync.</p>

<?php } ?>

<?php if( $active_tab == 'tab2' ) {  
?>

        <table class="widefat">
        
        <thead>
    		<tr>
        		<th>Original Affiliate Link</th>
        		<th>New Display Link</th>
            	<th>Action</th>
    		</tr>
		</thead>
        <tfoot>
    		<tr>
  				<th>Original Affiliate Link</th>
        		<th>New Display Link</th>
            	<th>Action</th>
   			</tr>
		</tfoot>
        <tbody>
  
        <?php
		echo $body;
		?>
        </tbody>
        
        </table>
        
        <br/>
        
        <a href="?page=Link_Cloaking&tab=tab3" class="button-secondary">Add Cloaked Link</a>
        
        <form name="delete_link_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
</form>
        
        <?php
    	}  
		?>  

<?php if( $active_tab == 'tab3' ) {  ?>
<h2>Add Cloaked Link</h2>

    <form name="link_cloaking_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"> 
     
        <input type="hidden" name="add_link" value="Y">  
        
      
       <?php _e("Original Affiliate Link: " ); ?><input type="text" name="link_cloaking_affiliate_link" value="http://" size="40"><?php _e(" ex: http://www.tlqpvt.com/?affiliate_id=1343234&pub_id=12182736&ad_id=2321&sub_id=index" ); ?>
       <br/>
        <span class="help-block">Enter your affiliate url in it's original format. Make sure the url is exactly how it appears on your WordPress website.</span>
        <br/><br/>
        
        <?php _e("Display Link: " ); ?><input type="text" name="link_cloaking_cloaked_link" value="http://" size="40"><?php _e(" ex: http://www.amazon.com/product/1" ); ?><br/>  
        <span class="help-block">Enter a url that'd you'd like your visitors to see instead of the original affiliate link.</span>
        
    
        <p class="submit">  
        <input type="submit" name="Submit" class="button-primary" value="<?php _e('Submit', 'link_cloaking_trdom' ) ?>" />  
        </p>  
    </form>  

<?php } ?>

<?php if( $active_tab == 'tab4' ) {  ?>

<h2>Link Existing Account</h2>
  
    <form name="existing_account_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"> 
     
        <input type="hidden" name="link_account" value="Y">  
   
   <p> If you already have an existing AffiliateDefense account enter your API key and your existing links will be imported.</p>          
 <p><?php _e("API Key: " ); ?><input type="text" name="link_cloaking_api_key" value="<?php echo $apiKeyFormValue; ?>" size="20"></p> 
        
   
        <p class="submit">  
        <input type="submit" name="Submit" class="button-primary" value="<?php _e('Submit', 'existing_account_trdom' ) ?>" />  
        </p>  
    </form>  

<?php } ?>

</div>