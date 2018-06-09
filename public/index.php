<?php
/*
 * Copyright 2018 DEVZONE TECH Ltd.
 * 
 * WebSiteSnapshot class PHP example
 * Read web site address by domain name and create thumbnail image
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR 
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR 
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * Free for non-commercial use. 
 * For commercial usage, please contact DEVZONE TECH Ltd. https://www.devzonetech.com/
 * 
 * SOFTWARE REQUIREMENTS:
 * PHP5 >= 5.5.0, PHP7
 * imagick module enabled
 * 
 * installed xvfb
 * installed wkhtmltopdf
 * installed convert
 */

include('../libs/WebSiteSnapshot.php');

$webSiteSnapshot = new WebSiteSnapshot('google.com', 250, 200);
echo '<img src="'.$webSiteSnapshot->getSnapshotUrl().'" alt="" />';

$webSiteSnapshot = new WebSiteSnapshot('devzonetech.com', 300, 250);
echo '<img src="'.$webSiteSnapshot->getSnapshotUrl().'" alt="" />';

?>
