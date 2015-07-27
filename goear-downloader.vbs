Set oShell = WScript.CreateObject ("WScript.Shell")
url = InputBox("Enter a song or playlist")
cmd = "cmd.exe /K php -f goear-downloader.php -- " & url & " & exit"
oShell.run cmd
