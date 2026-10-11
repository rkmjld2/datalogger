#include <ESP8266WiFi.h>
#include <WiFiClient.h> 
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>

/* Set these to your desired credentials. */
const char *ssid = "Airtel_56"; //ENTER YOUR WIFI SETTINGS
const char *password = "Raviuma5658";

//Web/Server address to read/write from 
const char *host = "192.168.1.10";   //https://circuits4you.com website or IP address of server

//=======================================================================
//                    Power on setup
//=======================================================================
//int adcvalue=analogRead(A0);

 int  adcvalue1, adcvalue2, adcvalue3;
void setup() {
  delay(1000);
  Serial.begin(115200);
  WiFi.mode(WIFI_OFF);        //Prevents reconnection issue (taking too long to connect)
  delay(1000);
  WiFi.mode(WIFI_STA);        //This line hides the viewing of ESP as wifi hotspot
  
  WiFi.begin(ssid, password);     //Connect to your WiFi router
  Serial.println("");

  Serial.print("Connecting");
  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  //If connection successful show IP address in serial monitor
  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());  //IP address assigned to your ESP
}
//=======================================================================
//                    Main Program Loop
//=======================================================================
void loop() {
  HTTPClient http;    //Declare object of class HTTPClient
String ADCData1, ADCData2, ADCData3, postData;
 //String ADCData, station, postData;

  //String ADCData, station, postData;
  //int adcvalue=analogRead(A0);  //Read Analog value of LDR
  //ADCData = String(adcvalue);   //String to interger conversion
  //station = "A";
 ADCData1=String(adcvalue1);
 ADCData2=String(adcvalue2);
 ADCData3=String(adcvalue3);
// postData = "status=" + ADCData + "&station=" + station ;
//  postData = "value1=" + ADCData1 + "&value2=" + ADCData2 + "&value3=" + ADCData3;
  postData = "value1=" +ADCData1+ "&value2=" + ADCData2 +"&value3=" +ADCData3;
 // http.begin("http://192.168.43.128/c4yforum/postdemo2.php");              //Specify request destination
 http.begin("http://192.168.1.10/datalogger/postdemo.php");  
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    //Specify content-type header

  int httpCode = http.POST(postData);   //Send the request
  String payload = http.getString();    //Get the response payload

  Serial.println(httpCode);   //Print HTTP return code
  Serial.println(payload);    //Print request response payload

  http.end();  //Close connection
  
  Serial.println(ADCData1);
  Serial.println(ADCData2);
  Serial.println(ADCData3);
 adcvalue1=adcvalue1+10;
 adcvalue2=adcvalue2+20;
 adcvalue3=adcvalue3+30;

  
  delay(5000);  //Post Data at every 5 seconds
}





