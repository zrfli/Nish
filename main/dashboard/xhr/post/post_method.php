<?php
if($_SERVER['REQUEST_URI'] != '/xhr/dashboard/post/post_method'){ header('Location: /not-found'); exit(); }

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/src/database/config.php';

$dbClass = new misyDbInformation();
//$logger = new MisyLogger($dbClass);	

$errors = []; $data = []; $postMethod = null;

header("Content-type: application/json; charset=utf-8");

try {
    $db = new \PDO($dbClass -> misyGetDb('mysql'), $dbClass -> getUser(), $dbClass -> getPassword());
    $auth = new \Delight\Auth\Auth($db);

    if (!$auth -> check()){ $errors['isLogged'] = false; }
    //if (!$auth -> hasRole(\Delight\Auth\Role::STUDENT)) { $errors['permission'] = 'permission denied!'; }
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){ $errors['method'] = 'method not accepted!'; }
    if (empty($_POST['postMethod'])){ $errors['postMethod'] = 'post method id is not correct!'; } else { $postMethod = htmlspecialchars($_POST['postMethod']); }   

    if (!empty($errors)) {
        $data['status'] = false;
        $data['statusCode'] = 406;
        $data['errors'] = $errors;
    } else {
        $data['status'] = 'success'; 
        $data['statusCode'] = 200; 
        $data['token'] = \Delight\Auth\Auth::createRandomString(24); 
        $data['userId'] = \Delight\Auth\Auth::createUuid();

        if($postMethod == 'create_page'){
          $data['data'] = '<h2 class="mb-2 text-lg font-medium">Sayfa Oluştur</h2>
                            <div class="text-sm text-gray-600">
                              <p><span class="text-red-400">**</span>Açıklama bölümüne öğrenci adı, soyadı, okul numarası veya TC kimlik numarası yazılmalıdır.</p>
                              <p><span class="text-red-400">**</span>Ödeme sonrasında dekontların sisteme yüklenip onaya gönderilmesi gereklidir.</p>
                            </div>
                            <p class="mt-4 text-sm text-gray-500" id="actionMessage">Devam etmek istediğiniz bankayı seçiniz:</p>
                            <div class="grid grid-cols-3 mt-1 md:grid-cols-4 gap-3" id="bankPanel">
                              <div class="flex items-center cursor-pointer rounded-md border-2 min-h-14 bg-white p-2 shadow-md hover:border-blue-300" onclick="bankTransferInformation(1);">
                                <img class="h-auto w-36 object-cover" src="https://upload.wikimedia.org/wikipedia/commons/c/cb/Al_Baraka_Banking_Group_Logo.svg" alt="" />
                              </div>
                              <div class="flex items-center cursor-pointer rounded-md border-2 min-h-14 bg-white p-2 shadow-md hover:border-blue-300" onclick="bankTransferInformation(2);">
                                <img class="h-auto w-36 object-cover" src="https://www.qnbfinansbank.com/medium/GalleryImage-Image-195-2x.vsf" alt="" />
                              </div>
                              <div class="flex items-center cursor-pointer rounded-md border-2 min-h-14 bg-white p-2 shadow-md hover:border-blue-300" onclick="bankTransferInformation(3);">
                                <img class="h-auto w-36 object-cover" src="https://upload.wikimedia.org/wikipedia/commons/9/95/TEB_LOGO.png" alt="" />
                              </div>
                              <div class="flex items-center cursor-pointer rounded-md border-2 min-h-14 bg-white p-2 shadow-md hover:border-blue-300" onclick="bankTransferInformation(4);">
                                <img class="h-auto w-36 object-cover" src="https://upload.wikimedia.org/wikipedia/commons/f/fd/Garanti_BBVA_2019.svg" alt="" />
                              </div>
                              <div class="flex items-center cursor-pointer rounded-md border-2 min-h-14 bg-white p-2 shadow-md hover:border-blue-300" onclick="bankTransferInformation(5);">
                                <img class="h-auto w-36 object-cover" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Akbank_logo.svg" alt="" />
                              </div>
                            </div>
                            <div class="mt-4" style="display:none;" id="paymentDetailsTable">
                              <table class="w-full border-2 text-left text-sm">
                                <thead class="bg-gray-100 text-xs">
                                  <tr>
                                    <!--<th class="px-6 py-3">Banka</th>-->
                                    <th class="px-6 py-3">Alıcı</th>
                                    <th class="px-6 py-3">IBAN</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr class="bg-white text-xs">
                                    <!--<td class="px-6 py-4" id="transferredBankName"></td>-->
                                    <td class="px-6 py-4">T.C. İstanbul Yeni Yüzyıl Üniversitesi</td>
                                    <td class="px-6 py-4" id="transferredBankIban"></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <div class="flex w-full items-center justify-center mt-4" id="documentPanel" style="display:none;">
                              <label for="documentInput" class="flex h-auto w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pb-2 pt-2">
                                  <p class="mb-2 text-sm text-gray-500">Yüklemek için tıklayın.</p>
                                  <p class="text-xs text-gray-500">PDF, PNG, JPG, JPEG</p>
                                </div>
                                <input id="documentInput" accept=".pdf,.png,.jpg,.jpeg" type="file" class="hidden" onchange="handleDocumentInput(this);" />
                              </label>
                            </div>
                            <div class="p-4 border mt-4" id="uploadingFilePanel" style="display:none;">
                              <!-- Uploading File Content -->
                              <div class="mb-2 flex justify-between items-center">
                                <div class="flex items-center gap-x-2">
                                  <span class="w-8 h-8 flex justify-center items-center border border-gray-200 text-gray-500 rounded-lg">
                                    <svg class="flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                  </span>
                                  <div>
                                    <p class="text-xs font-medium text-gray-800" id="fileName"></p>
                                    <p class="text-xs text-gray-500" id="fileSize"></p>
                                  </div>
                                </div>
                                <div id="fileActionPanel" style="display:none;">
                                  <span class="text-gray-500 hover:text-gray-900 cursor-pointer" onclick="abortUpload();">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h4v16H6zm8 0h4v16h-4z"/></svg>
                                  </span>
                                </div>
                              </div>
                              <!-- End Uploading File Content -->
                              <!-- Progress Bar -->
                              <div class="flex items-center justify-between gap-x-2 whitespace-nowrap">
                                <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden ">
                                  <div class="flex flex-col justify-center rounded-full overflow-hidden bg-blue-500 text-xs text-white text-center whitespace-nowrap transition duration-500" id="barWidth"></div>                                  </div>
                                  <span class="text-xs text-gray-800 font-medium" id="barTotal">0%</span>
                              </div>
                              <!-- End Progress Bar -->
                            </div>
                            <div class="flex justify-end mt-4" id="actionBtnPanel" style="display:none;">
                              <button type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="payWithBankTransfer();">
                                Onaya gönder
                              </button>
                            </div>';

          $data['bankTransferInformation'][1]['name'] = 'Albarakatürk Katılım Bankası';
          $data['bankTransferInformation'][1]['branch'] = null;
          $data['bankTransferInformation'][1]['iban'] = 'TR39 0020 3000 0118 0769 0014 84';

          $data['bankTransferInformation'][2]['name'] = 'QNB Finansbank';
          $data['bankTransferInformation'][2]['branch'] = null;
          $data['bankTransferInformation'][2]['iban'] = 'TR82 0011 1000 0000 0047 3085 51';

          $data['bankTransferInformation'][3]['name'] = 'Türk Ekonomi Bankası';
          $data['bankTransferInformation'][3]['branch'] = null;
          $data['bankTransferInformation'][3]['iban'] = 'TR70 0003 2000 0000 0015 9636 00';
        } else if($postMethod == 'create_content') {
          $data['data'] = '<script type="text/javascript" src="assets/dashboard/tinymce/tinymce.min.js"></script>
                            <script>
                              if (typeof post_image_upload_handler === "undefined") {
                                const post_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
                                  const xhr = new XMLHttpRequest();
                                  xhr.withCredentials = false;
                                  xhr.open("POST", "xhr/dashboard/post/upload_post_content_images");

                                  xhr.upload.onprogress = (e) => { progress(e.loaded / e.total * 100); };

                                  xhr.onload = () => {
                                    if (xhr.status === 403) {
                                      reject({ message: "HTTP Error: " + xhr.status, remove: true });
                                      return;
                                    }

                                    if (xhr.status < 200 || xhr.status >= 300) {
                                      reject("HTTP Error: " + xhr.status);
                                      return;
                                    }

                                    const json = JSON.parse(xhr.responseText);

                                    if (!json || typeof json.location != "string") {
                                      reject("Invalid JSON: " + xhr.responseText);
                                      return;
                                    }

                                    resolve(json.location);
                                  };

                                  xhr.onerror = () => {
                                    reject("Image upload failed due to a XHR Transport error. Code: " + xhr.status);
                                  };

                                  const formData = new FormData();
                                  formData.append("handledImages", blobInfo.blob(), blobInfo.filename());

                                  xhr.send(formData);
                                });
                            
                                tinymce.init({
                                  selector: "textarea#contentHtmlMarkup",
                                  height: 500,
                                  license_key: "gpl",
                                  plugins: [
                                    "advlist", "autolink", "lists", "link", "image", "charmap",
                                    "anchor", "searchreplace", "visualblocks",
                                    "insertdatetime", "media", "table", "wordcount"
                                  ],
                                  toolbar: "undo redo | blocks | " +
                                  "bold italic backcolor | alignleft aligncenter " +
                                  "alignright alignjustify | bullist numlist outdent indent | " +
                                  "removeformat | help",
                                  content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
                                  images_upload_handler: post_image_upload_handler
                                });
                              }
                            </script>
                            <h2 class="text-lg font-medium mb-2">Gönderi Oluştur</h2>
                            <div id="cardDetailsContent" class="grid gap-2 mt-3 grid-cols-1 items-center">
                                <div class="grid gap-2 grid-cols-2">
                                    <div class="col-span-2">
                                        <label for="contentTitle" class="block mb-2 text-sm font-medium text-gray-900">
                                            <span class="text-red-400">*</span>Başlık
                                        </label>
                                        <input type="text" name="contentTitle" id="contentTitle" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 appearance-none focus:outline-none focus:ring-0 focus:border-black font-medium" placeholder="------------" required />
                                    </div>
                                    <div class="col-span-2">
                                        <label for="contentCoverImage" class="mb-2 block text-sm font-medium text-gray-900"> <span class="text-red-400">*</span>Kapak Fotoğrafı</label>
                                        <input id="coverImageInput" accept=".webp,.png,.jpg,.jpeg,.gif" type="file" onchange="handleDocumentInput(this);" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="contentCoverImage">
                                        <p class="mt-1 text-xs text-gray-600" id="contentCoverImage">SVG, PNG, JPG or GIF.</p>
                                    </div>
                                    <div class="col-span-2">
                                        <label for="albumInput" class="mb-2 block text-sm font-medium text-gray-900"> <span class="text-red-400">*</span>Fotoğraf Albümü</label>
                                        <input id="albumInput" multiple accept=".webp,.png,.jpg,.jpeg,.gif" type="file" onchange="albumInput(this);" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" aria-describedby="contentCoverImage">
                                        <div class="image-preview mt-4 flex gap-4 flex-wrap" id="imagePreview"></div>
                                    </div>
                                    <div class="col-span-1">
                                        <label for="contentLanguage" class="mb-2 block text-sm font-medium text-gray-900">
                                          <span class="text-red-400">*</span>İçerik Dili
                                        </label>
                                        <select id="contentLanguage" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-black font-medium">
                                            <option selected value="tr">Türkçe</option>
                                            <option value="en">İngilizce</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <label for="contentType" class="mb-2 block text-sm font-medium text-gray-900">
                                          <span class="text-red-400">*</span>İçerik Türü
                                        </label>
                                        <select id="contentType" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-black font-medium">
                                            <option selected disabled>--</option>
                                            <option value="news">Haber</option>
                                            <option value="announcements">Duyuru</option>
                                            <option value="events">Etkinlik</option>
                                            <option value="research">Araştırma</option>
                                            <option value="achievements">Başarı</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4">
                                  <label for="contentType" class="mb-2 block text-sm font-medium text-gray-900">
                                    <span class="text-red-400">*</span>İçerik
                                  </label>
                                  <textarea id="contentHtmlMarkup"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4" id="createContentBtn">
                              <button type="button" class="text-white flex bg-black hover:bg-black/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onclick="createContent();">İçerik Oluştur</button>
                            </div>';
        } else if($postMethod == 'iyzipay' OR $postMethod == 'paytr') {
          $data['data'] = '<div role="status" class="flex h-56 w-full animate-pulse bg-gray-300"></div>';
        } else {
          $data['data'] = '<div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-gray-600" role="alert">
                              <p class="text-sm text-yellow-600 text-center">İşlem sırasında bir hata oluştu. Lütfen tekrar deneyiniz.</p>
                          </div>';
        }
    }
    
} catch (\Throwable $th) {
    //$logger->logError($e, [details -> $e->getMessage(), 'user_id' -> $auth -> getUserId()], 'GET_PAYMENT_METHOD');
    die();
}

$db = null;

echo json_encode($data, JSON_UNESCAPED_UNICODE);