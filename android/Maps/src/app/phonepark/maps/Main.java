package app.phonepark.maps;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.text.DecimalFormat;
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
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.Toast;

public class Main extends Activity

{

/** Called when the activity is first created. */
	

@Override

	public void onCreate(Bundle savedInstanceState)
	{
	if (android.os.Build.VERSION.SDK_INT > 9) {

	      StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();

	      StrictMode.setThreadPolicy(policy);

	    }
		super.onCreate(savedInstanceState);

		setContentView(R.layout.main);

		final EditText et1 = (EditText) findViewById(R.id.editText1);
		final EditText et2 = (EditText) findViewById(R.id.editText2);
		final Button b1 = (Button) findViewById(R.id.button1);
		final Button b2 = (Button)findViewById(R.id.button2);
		final RadioGroup rg = (RadioGroup)findViewById(R.id.radioGroup1);
		final Spinner sp1 = (Spinner) findViewById(R.id.spinner1);
		


			/* Use the LocationManager class to obtain GPS locations */

		LocationManager mlocManager = (LocationManager)getSystemService(Context.LOCATION_SERVICE);

		LocationListener mlocListener = new LocationListener() {
			
			public void onLocationChanged(final Location loc)

			{
				
						Double Lati =loc.getLatitude();
				        DecimalFormat df = new DecimalFormat("#.####");
				        final String Latitude = df.format(Lati);
				        et1.setText(Latitude);
						Double Longi =loc.getLongitude();
						final String Longitude = df.format(Longi);
						et2.setText(Longitude);
				

								
					b1.setOnClickListener(new OnClickListener() {
					
					@Override
					public void onClick(View v) {
						String Lat =et1.getText().toString();
						String Lon = et2.getText().toString();
						
						
						String url1 = "http://rajak.me/input.php?Lati="+Lat+"&Longi="+Lon;
						HttpPost httpPost1 = new HttpPost(url1);
						HttpClient httpClient1 = new DefaultHttpClient();
						try {
							HttpResponse httpResponse1 = httpClient1.execute(httpPost1);
							
							HttpEntity entity = httpResponse1.getEntity();
						    InputStream is = entity.getContent();

						       //converting response to string
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
						       String addresses[] = result.split(":");
						
						ArrayAdapter<String> adapter = 
								new ArrayAdapter<String>(getApplicationContext(), android.R.layout.simple_spinner_item);

						
						for(int i=0;i<addresses.length;i++)
						{
							adapter.add(addresses[i]);
						}
						sp1.setAdapter(adapter);
						
						

						      

										
						} catch (ClientProtocolException e1) {
							e1.printStackTrace();
						} catch (IOException e1) {
							e1.printStackTrace();
						} 

						
					}
				});
				
					sp1.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
						public void onItemSelected(AdapterView<?> parent, View view, int pos, long id) {
					        parent.getItemAtPosition(pos);
					        
					    }
					    public void onNothingSelected(AdapterView<?> parent) {
					    }
					    
					    
					});
					
					b2.setOnClickListener(new OnClickListener() {
						
						@Override
						public void onClick(View v) {
							String address = String.valueOf(sp1.getSelectedItem());
							int option = rg.getCheckedRadioButtonId();
							 
						    RadioButton isParked = (RadioButton) findViewById(option);
						    String parkStatus = isParked.getText().toString();
						    
						    //String url1 = "http://rajak.me/storedb.php?address='"+address+"'&status='"+parkStatus+"'";
						    String url2 = "http://rajak.me/storedb.php";
							HttpPost httpPost2 = new HttpPost(url2);
							
							List<BasicNameValuePair> nameValuePairs = new ArrayList<BasicNameValuePair>(2);
				            nameValuePairs.add(new BasicNameValuePair("add",address));
				            nameValuePairs.add(new BasicNameValuePair("status",parkStatus));
				            
							try {
								
								httpPost2.setEntity(new UrlEncodedFormEntity(nameValuePairs));
								HttpClient httpClient2 = new DefaultHttpClient();
								HttpResponse httpResponse2 = httpClient2.execute(httpPost2);
								
								HttpEntity entity = httpResponse2.getEntity();
							    InputStream is = entity.getContent();

							       //converting response to string
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

		mlocManager.requestLocationUpdates( LocationManager.GPS_PROVIDER, 0, 20, mlocListener);

	}


}
/* End of Main Activity */