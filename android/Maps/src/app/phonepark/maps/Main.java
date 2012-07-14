package app.phonepark.maps;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import android.app.Activity;
import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Toast;

public class Main extends Activity

{

	/** Called when the activity is first created. */


	@Override

	public void onCreate(Bundle savedInstanceState)
	{
		//Correcting version issues with Android SDK
		if (android.os.Build.VERSION.SDK_INT > 9){
			StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
			StrictMode.setThreadPolicy(policy);
		}
		super.onCreate(savedInstanceState);
		setContentView(R.layout.main);
		
		//Declaring the form fields - present in the main.xml (GUI)
		final EditText et1 = (EditText) findViewById(R.id.editText1);
		final EditText et2 = (EditText) findViewById(R.id.editText2);
		final EditText et3 = (EditText) findViewById(R.id.editText3);
		final Button b1 = (Button) findViewById(R.id.button1);
		final Button b2 = (Button)findViewById(R.id.button2);
		final RadioGroup rg = (RadioGroup)findViewById(R.id.radioGroup1);

		/* Use the LocationManager class to obtain GPS locations */

		LocationManager mlocManager = (LocationManager)getSystemService(Context.LOCATION_SERVICE);
		LocationListener mlocListener = new LocationListener() {
			@Override
			public void onLocationChanged(final Location loc)
			{
				//On Location Changed - This module is executed
				Double Lati =loc.getLatitude();
				final String Latitude = Lati+"";
				et1.setText(Latitude);
				Double Longi =loc.getLongitude();
				final String Longitude = Longi+"";
				et2.setText(Longitude);
				//When Button b1 - GetAddress is clicked
				b1.setOnClickListener(new OnClickListener() {

					@Override
					public void onClick(View v) {
						//Value of Edit Text 1 and 2 are assigned to String Lat and Lon
						String Lat =et1.getText().toString();
						String Lon = et2.getText().toString();
						//url is constructed with Lat and Lon as arguments
						String url1 = "http://rajak.me/input.php?Lati="+Lat+"&Longi="+Lon;
						//post http request
						HttpPost httpPost1 = new HttpPost(url1);
						HttpClient httpClient1 = new DefaultHttpClient();
						try {
							//getting http response
							HttpResponse httpResponse1 = httpClient1.execute(httpPost1);
							HttpEntity entity = httpResponse1.getEntity();
							InputStream is = entity.getContent();
							BufferedReader reader = new BufferedReader(new InputStreamReader(is,"iso-8859-1"),8);
							StringBuilder sb = new StringBuilder();
							String line = null;
							while ((line = reader.readLine()) != null) 
							{
								sb.append(line);
							}
							is.close();
							String result = "";
							result = sb.toString();
							//assign the address in the result variable to EditText 3 - Address
							et3.setText(result);
						} catch (ClientProtocolException e1) {
							e1.printStackTrace();
						} catch (IOException e1) {
							e1.printStackTrace();
						} 
					}
				});

				//Button b2 - Submit is clicked
				b2.setOnClickListener(new OnClickListener() {

					@Override
					public void onClick(View v) {
						String address = et3.getText().toString();
						int option = rg.getCheckedRadioButtonId();
						RadioButton isParked = (RadioButton) findViewById(option);
						//Radio button value is stored in variable
						String parkStatus = isParked.getText().toString();
						String url2 = "http://rajak.me/storedb.php";
						HttpPost httpPost2 = new HttpPost(url2);
						List<BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>(2);
						//address and parking status variables are passed along with URL - like post method
						nameValuePairs.add(new BasicNameValuePair("add",address));
						nameValuePairs.add(new BasicNameValuePair("status",parkStatus)); 
						try {		
							httpPost2.setEntity(new UrlEncodedFormEntity(nameValuePairs));
							HttpClient httpClient2 = new DefaultHttpClient();
							HttpResponse httpResponse2 = httpClient2.execute(httpPost2);
							HttpEntity entity = httpResponse2.getEntity();
							InputStream is = entity.getContent();
							BufferedReader reader = new BufferedReader(new InputStreamReader(is,"iso-8859-1"),8);
							StringBuilder sb = new StringBuilder();
							String line = null;
							while ((line = reader.readLine()) != null) 
							{
								sb.append(line + "\n");
							}
							is.close();

							String result = "";
							result = sb.toString();
							//httpresponse result is displayed as toast message
							Toast toast = Toast.makeText(getApplicationContext(), result, Toast.LENGTH_SHORT);
							toast.show();

						}

						catch (ClientProtocolException e1) {
							e1.printStackTrace();
						} catch (IOException e1) {
							e1.printStackTrace();
						} 



					}
				});





			}

			@Override

			public void onProviderDisabled(String provider)

			{	

			}

			@Override

			public void onProviderEnabled(String provider)

			{

			}

			@Override

			public void onStatusChanged(String provider, int status, Bundle extras)

			{

			}	


		};

		mlocManager.requestLocationUpdates( LocationManager.GPS_PROVIDER, 0, 0, mlocListener);

	}


}
/* End of Main Activity */