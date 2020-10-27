// Buttons
const win_InstallerButton = document.getElementById("win_installerButton");
const win_UnpackedButton = document.getElementById("win_unpackedButton");
const win_Unpacked32Button = document.getElementById("win_unpacked32Button");
const linux_DebButton = document.getElementById("linux_debButton");
const linux_TarGzButton = document.getElementById("linux_targzButton");

// Buttons links
var win_InstallerDownload = document.getElementById("win_InstallerDownloadLink");
var win_UnpackedDownload = document.getElementById("win_UnpackedDownloadLink");
var win_Unpacked32Download = document.getElementById("win_Unpacked32DownloadLink");
var linux_DebDownload = document.getElementById("linux_DebDownloadLink");
var linux_TarGzDownload = document.getElementById("linux_TarGzDownloadLink");

let request = new XMLHttpRequest();
request.open("GET", "https://api.github.com/repos/ampersoftware/Creators.TF-Community-Launcher/releases/latest");
request.send();

request.onload = () => {
    if (request.status === 200) {
        var answer = JSON.parse(request.response);
        // We get the download links and apply them to the "a" tags inside the buttons
        var downloadLink_winInstaller = answer.assets[2].browser_download_url;
        var downloadLink_winUnpacked = answer.assets[6].browser_download_url;
        var downloadLink_winUnpacked32 = answer.assets[5].browser_download_url;
        var downloadLink_linuxDeb = answer.assets[1].browser_download_url;
        var downloadLink_linuxTarGz = answer.assets[0].browser_download_url;
        win_InstallerDownload.href = downloadLink_winInstaller;
        win_UnpackedDownload.href = downloadLink_winUnpacked;
        win_Unpacked32Download.href = downloadLink_winUnpacked32;
        linux_DebDownload.href = downloadLink_linuxDeb;
        linux_TarGzDownload.href = downloadLink_linuxTarGz;
        // We get the latest version and apply them to the buttons' text
        var version = answer.tag_name;
        win_InstallerButton.innerText = "Installer (" + version + ")";
        win_UnpackedButton.innerText = "Unpacked (" + version + ")";
        win_Unpacked32Button.innerText = "Unpacked (" + version + ")";
        linux_DebButton.innerText = "Deb (" + version + ")";
        linux_TarGzButton.innerText = "tar.gz (" + version + ")";
    } else {
        console.log(`ERROR. EITHER JOTA IS STUPID OR GITHUB IS DOWN, PROBABLY THE FORMER. Status: ${request.status} -- Message: ${request.statusText}`);
    }
}
