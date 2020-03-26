<?php


class Loganalysis
{

    /**
     * 
     * loads log file and saves it as json
     * 
     * @param string $logFilename
     * 
     * @return bool
     */
    public function loadLog(
        string $inputFilename = 'logs/epa-http.txt',
        string $outputFilename = 'logs/out.json'
    ): bool
    {
        $loganalyis =  new Loganalysis();

        $lines = file($inputFilename);
        if($lines === false){
            return false;
        }

        $logOut = [];
        foreach ($lines as $line) {
            $logOut[] = $this->parseLogEntry($line);
        }

        file_put_contents(
            $outputFilename,
            json_encode($logOut, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
        );

        return true;
    }


    /**
     * 
     * parses line from log and returns LogEntry object
     * 
     * @param string $line
     * 
     * @return LogEntry
     */
    private function parseLogEntry(string $line = null): LogEntry
    {
        // TODO: make this with regex, also better 1st, 3rd and 4th group
        // $pattern='([^\s]+) (\[\d{2}:\d{2}:\d{2}:\d{2}\]) "(.*?)" (\d{3}) (.*)';
        // preg_match($pattern, $line, $request);
        $line = explode(' ', trim($line));

        // $line[0]; // host
        $line[1] = explode(':',str_replace(['[',']'],['',''],$line[1])); // time
        $line[2] = str_replace('"','',$line[2]); // request method
        // $line[3]; // request url
        $line[4]=explode('/',str_replace('"','',$line[4])); // request protocol + version
        // $line[5]; // response code
        $line[6] = (empty($line[6]) ? '' : $line[6]); // document size

        $log_entry = new LogEntry();
        $date_time = new LogDateTime();
        $request = new LogRequest();

        $date_time->day = $line[1][0];
        $date_time->hour = $line[1][1];
        $date_time->minute = $line[1][2];
        $date_time->second = $line[1][3];

        $request->method = $line[2];
        $request->url = $line[3];
        $request->protocol = $line[4][0];
        $request->protocol_version = (empty($line[4][1]) ? '' : $line[4][1] );

        $log_entry->host = $line[0];
        $log_entry->dateTime = $date_time;
        $log_entry->request = $request;
        $log_entry->response_code = (empty($line[5]) ? '' : $line[5]);
        $log_entry->document_size = ($line[6] === '-' ? 0 : $line[6] );

        return $log_entry;
    }
}


/**
 * 
 * Helper classes for log parsing
 * 
 */
class LogEntry
{
    public string $host;
    public object $dateTime;
    public object $request;
    public string $response_code;
    public string $document_size;
}

class LogDateTime
{
    public string $day;
    public string $hour;
    public string $minute;
    public string $second;
}

class LogRequest
{
    public string $method;
    public string $url;
    public string $protocol;
    public string $protocol_version;
}

