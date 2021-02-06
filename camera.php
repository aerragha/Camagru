<?php
require_once("config/setup.php");
session_start();
if (!isset($_SESSION["id"]))
    header("location: index.php");
    if (isset($_GET["post"]))
    {
        $image = $_GET["post"];
        $cmd = $cn->prepare("DELETE FROM POSTS WHERE image = :image AND id_user = :id_user");
        $cmd->bindParam(':id_user', $_SESSION["id"]);
        $cmd->bindParam(':image', $image);
        try
        {
            $cmd->execute();
        }
        catch (PDOException $ex)
        {
            echo 'query error!';
        }
    }
require_once("header.php");
?>

<div class="container">
            <div class="row mt-5">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mt-2">
                        <button type="submit" id="upfile" class="btn btn-custom btn-block"><i class="fa fa-upload"></i> Upload image</button>
                        
                        <button type="submit" id="usecam" class="btn btn-custom btn-block"><i class="fa fa-camera"></i> Camera</button>
                       
                        <div class="upfile_div" id="up_img_div">
                                <input type="file" name="pic" style="display:none;" accept="image/*" id="input_img" class="form-control">
                                <hr>
                                <div class="filter_canva">
                                    <canvas id="canvas" width="536" height="410"></canvas>
                                    <img src="" id="up_effect">
                                </div>
                                <hr>
                                <div class="effect_div">
                                    <select class="form-control" id="up_img_select">
                                        <option disabled value="" selected>--Select--</option>
                                        <option value="effect/cat.png">Cat</option>
                                        <option value="effect/emoji.png">Smile Emoji</option>
                                        <option value="effect/lion.png">Lion</option>
                                    </select>
                                </div>
                                <hr>
                                <button class="btn btn-custom btn-block" id="up_image">Save Image</button>
                        </div>
                        <hr>
                        <div class="camera_div" id="cam_div">
                        <div class="filter_canva">
                            <video id="video" width="536" autoplay></video>
                            <img src="" id="snap_effect">
                        </div>
                                <hr>
                                <div class="effect_div">
                                    <select class="form-control" id="snap_img_select">
                                        <option disabled value="" selected>--Select--</option>
                                        <option value="effect/cat.png">Cat</option>
                                        <option value="effect/emoji.png">Smile Emoji</option>
                                        <option value="effect/lion.png">Lion</option>
                                    </select>
                                </div>
                                <hr>
                                <button class="btn btn-custom btn-block" id="snap">Snap Photo</button>
                                
                        </div>
                        <canvas id="canvas2" width="536" height="410"></canvas>
                        
                    </div>
                
                    <div class="col-lg-6 col-md-6 col-sm-12  col-12 mt-2 ">
                            <div class="row myimg_div m-auto">
                                        <div class="col-12" id="my_img_div">
                                        <?
                                            function get_nb_like($id_post, $cn)
                                            {
                                                $cmd = $cn->prepare("SELECT COUNT(id_post) FROM Likes WHERE id_post = :id_post");
                                                $cmd->bindParam(':id_post', $id_post);
                                                try
                                                {
                                                    $cmd->execute();
                                                    return($cmd->fetchColumn());
                                                }
                                                catch (PDOException $ex)
                                                {
                                                    echo 'query error!';
                                                }
                                            }
                                            function get_nb_comment($id_post, $cn)
                                            {
                                                $cmd = $cn->prepare("SELECT COUNT(id_post) FROM COMMENTS WHERE id_post = :id_post");
                                                $cmd->bindParam(':id_post', $id_post);
                                                try
                                                {
                                                    $cmd->execute();
                                                    return($cmd->fetchColumn());
                                                }
                                                catch (PDOException $ex)
                                                {
                                                    echo 'query error!';
                                                }
                                            }

                                            $cmd = $cn->prepare("SELECT id_post, image FROM POSTS WHERE id_user = :id_user ORDER BY id_post DESC");
                                            $cmd->bindParam(':id_user', $_SESSION["id"]);
                                            try
                                            {
                                                $cmd->execute();
                                            }
                                            catch (PDOException $ex)
                                            {
                                                echo 'query error!';
                                            }
                                            while ($result = $cmd->fetch())
                                            {
                                                echo '<div class="containere mt-2">
                                                        <img class="post_img_2" src="' . $result["image"] . '" alt="Notebook" style="width:100%;">
                                                        <div class="content">
                                                            <div class="cmt_like_ic">
                                                            <span class="mr-2"><i class="fa fa-heart" aria-hidden="true"></i> ' . get_nb_like($result["id_post"], $cn) . ' </span>
                                                            <span><i class="fa fa-comment" aria-hidden="true"></i> ' . get_nb_comment($result["id_post"], $cn) . ' </span> <br>
                                                            <a class="delete" href="camera.php?post=' . $result["image"] . '"><span><i class="fa fa-trash" aria-hidden="true"></i> Delete </span></a>
                                                            </div>
                                                        </div>
                                                    </div> ';
                                            }
                                        ?>
                                            
                                        </div>
                            </div>
                    </div>
            </div>
        </div>
        <canvas id="blank" width="536" height="410" style='display:none'></canvas>
        <footer class="footer text-center">
            © Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
    </footer>
    </body>
    <script>
        // camera zone

       document.querySelector(".camera_div").style.display = "none"; 
       document.querySelector(".upfile_div").style.display = "none"; 


        function camera()
        {
            if (navigator.mediaDevices === undefined) {
                navigator.mediaDevices = {};
            }
            if (navigator.mediaDevices.getUserMedia === undefined) {
                navigator.mediaDevices.getUserMedia = function(constraints) {

                    // First get ahold of the legacy getUserMedia, if present
                    var getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

                    // Some browsers just don't implement it - return a rejected promise with an error
                    // to keep a consistent interface
                    if (!getUserMedia) {
                    return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
                    }

                    // Otherwise, wrap the call to the old navigator.getUserMedia with a Promise
                    return new Promise(function(resolve, reject) {
                    getUserMedia.call(navigator, constraints, resolve, reject);
                    });
                }
            }
            
                var video = document.getElementById('video');

            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {

                // Older browsers may not have srcObject
                if ("srcObject" in video) {
                    video.srcObject = stream;
                } else {
                    // Avoid using this in new browsers, as it is going away.
                    video.src = window.URL.createObjectURL(stream);
                }
                video.onloadedmetadata = function(e) {
                    video.play();
                };
                })
                .catch(function(err) {
                    alert(`Error: ${err}`);
                    location.reload(true);
                });
        }
        // Elements for taking the snapshot
        const canvas2 = document.getElementById('canvas2');
        const context = canvas2.getContext('2d');
        const video = document.getElementById('video');

        // Trigger photo take
        document.getElementById("snap").addEventListener("click", function() {
            context.clearRect(0, 0, canvas2.width, canvas2.height);
            context.drawImage(video, 0, 0, 536, 410);
        });
       

        /////////////////////////////////////////////////////////////////////////////////


        const snap_btn = document.getElementById("snap");
        const save_btn = document.getElementById("up_image");
        const up_img = document.getElementById("up_img_select");
        const snap_img = document.getElementById("snap_img_select");
        const up_effect = document.getElementById("up_effect");
        const snap_effect = document.getElementById("snap_effect");

        snap_btn.style.pointerEvents = "none";
        save_btn.style.pointerEvents = "none";
        
        function hide_emoji()
        {
            snap_img.value = "";
            up_img.value = "";
            up_effect.style.display = "none";
            snap_effect.style.display = "none";
        }
        function check_canvas(canv)
        {
            if (canv.toDataURL() == document.getElementById('blank').toDataURL())
            {
                alert("Don't play with div css!!");
                return (0);
            }
            else
                return (1);
        }
        snap_img.addEventListener("change", function()
        {
            snap_effect.style.display = "none";
            if (snap_img.value == "")
                snap_btn.style.pointerEvents = "none";
            else if (values.indexOf(snap_img.value) > -1)
            {
                    snap_effect.src = snap_img.value;
                    snap_effect.style.display = "block";
                    snap_btn.style.pointerEvents = "painted";
            }
        })

        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        up_img.addEventListener("change", function()
        {
            if (up_img.value == "")
                save_btn.style.pointerEvents = "none";
            else if (values.indexOf(up_img.value) > -1 && check_canvas(canvas) == 1)
            {
                    up_effect.src = up_img.value;
                    up_effect.style.display = "block";
                    save_btn.style.pointerEvents = "painted";
            }
                
        })
        
        /////////////
        const extens = ["png", "jpg", "jpeg", "gif"];
        const input_img = document.getElementById("input_img");

        function check_image()
        {
            var ext = input_img.value.split('.').pop().toLowerCase();
            if (input_img.value != "")
            {   
                if (extens.indexOf(ext) > -1)
                {
                    file = input_img.files[0];
                    if (file.size < 2000000)
                        return (1);
                    else
                        alert("The Image is too big!");
                }
                else
                    alert("Please upload an image!");
            }
            else
                alert("No image uploaded!!");
            return (0);
        }

        
        input_img.addEventListener("change", function()
        {
            if (check_image() == 1)
            {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                var img = new Image;
                img.onload = function() {
                    if (img)
                    {
                        ctx.drawImage(img, 0, 0, 536, 410);
                        document.querySelector(".camera_div").style.display = "none";
                        document.querySelector(".upfile_div").style.display = "block";
                    }
                }
                img.src = URL.createObjectURL(input_img.files[0]);
            }
            else
            {
                document.querySelector(".camera_div").style.display = "none";
                document.querySelector(".upfile_div").style.display = "none";
                input_img.value = "";
            }
                
        })                                            
        
        ///////// btn save_image with ////////

        const my_img_div = document.getElementById("my_img_div");


        const values = ["effect/cat.png", "effect/emoji.png", "effect/lion.png"];
        save_btn.addEventListener("click", function()
        {
            if (values.indexOf(up_img.value) > -1)
            {
                if (check_canvas(canvas) == 1)
                {
                    var up_img_div = document.getElementById("up_img_div");
                    var info_div = up_img_div.getBoundingClientRect();
                    var info_effect = up_effect.getBoundingClientRect();
                    var coord_x = Math.floor(info_effect.left - info_div.left);
                    var coord_y = Math.floor(info_effect.top - info_div.top - 10);

                    if (info_effect.right < (info_div.right - 8))
                    {
                        var xhttp = new XMLHttpRequest();
                        var imgUrl = canvas.toDataURL();
                        var params = "imgUrl="+imgUrl+"&effect="+up_img.value+"&coord_x="+coord_x+"&coord_y="+coord_y;
                        xhttp.open("POST", "save_img.php", true);
                        xhttp.withCredentials = true;
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.onreadystatechange = function()
                        {
                            if (this.readyState == 4 && this.status == 200) 
                            {
                                my_img_div.innerHTML = this.responseText + my_img_div.innerHTML;
                            }
                        }
                        xhttp.send(params);
                    }
                }
            }
            else
                alert("Please make sure you choose an correct effect!!");
        });


        ///////// btn snap_Photo with canvas2 ////////
        snap_btn.addEventListener("click", function()
        {
            if (values.indexOf(snap_img.value) > -1)
            {
                if (check_canvas(canvas2) == 1)
                {
                    var cam_div = document.getElementById("cam_div");
                    var info_div = cam_div.getBoundingClientRect();
                    var info_effect = snap_effect.getBoundingClientRect();
                    var coord_x = Math.floor(info_effect.left - info_div.left);
                    var coord_y = Math.floor(info_effect.top - info_div.top);
            
                    if (info_effect.right < (info_div.right - 8))
                    {
                        var xhttp = new XMLHttpRequest();
                        var imgUrl = canvas2.toDataURL();
                        var params = "imgUrl="+imgUrl+"&effect="+snap_img.value+"&coord_x="+coord_x+"&coord_y="+coord_y;
                        xhttp.open("POST", "save_img.php", true);
                        xhttp.withCredentials = true;
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhttp.onreadystatechange = function()
                        {
                            if (this.readyState == 4 && this.status == 200) 
                            {
                                my_img_div.innerHTML = this.responseText + my_img_div.innerHTML;
                            }
                        }
                        xhttp.send(params);
                    }
                }
            }
            else
                alert("Please make sure you choose an correct effect!!");
        });


        ///////////////////////////////

        ////////// key press part ////////////////
            var from_top = 20;
            var from_left = 30;
            window.addEventListener("keypress", function(e)
            {
                if (up_effect.style.display != "none")
                {
                    var keyCode = e.which || e.keyCode;
                    if (keyCode == 56)
                    {
                        if (from_top > 2)
                        {
                            from_top--;
                            up_effect.style.top = from_top + "%";
                        }
                    }
                    else if (keyCode == 52)
                    {
                        if (from_left > 2)
                        {
                            from_left--;
                            up_effect.style.left = from_left + "%";
                        }
                    }
                    else if (keyCode == 50)
                    {
                        if (from_top < 43)
                        {
                            from_top++;
                            up_effect.style.top = from_top + "%";
                        }
                    }
                    else if (keyCode == 54)
                    {
                        if (from_left < 58)
                        {
                            from_left++;
                            up_effect.style.left = from_left + "%";
                        }
                    }
                }
            });


            var from_top_2 = 20;
            var from_left_2 = 30;
            window.addEventListener("keypress", function(e)
            {
                if (snap_effect.style.display != "none")
                {
                    var keyCode = e.which || e.keyCode;
                    if (keyCode == 119)
                    {
                        
                        if (from_top_2 > 2)
                        {
                            from_top_2--;
                            snap_effect.style.top = from_top_2 + "%";
                        }
                    }
                    else if (keyCode == 97)
                    {
                        
                        if (from_left_2 > 2)
                        {
                            from_left_2--;
                            snap_effect.style.left = from_left_2 + "%";
                        }
                    }
                    else if (keyCode == 115)
                    {
                        
                        if (from_top_2 < 43)
                        {
                            from_top_2++;
                            snap_effect.style.top = from_top_2 + "%";
                        }
                    }
                    else if (keyCode == 100)
                    {
                        
                        if (from_left_2 < 58)
                        {
                            from_left_2++;
                            snap_effect.style.left = from_left_2 + "%";
                        }
                    }
                }
            });
        ////////////////////////////



        //// this part for buttons
        const cam = document.getElementById("usecam");
        const fileimg = document.getElementById("upfile");
        

        cam.addEventListener("click", function()
        {
            hide_emoji();
            if (document.querySelector(".camera_div").style.display == "none")
            {
                document.querySelector(".camera_div").style.display = "block";
                document.querySelector(".upfile_div").style.display = "none";
                camera();
                input_img.value = "";
            }
            else
                document.querySelector(".camera_div").style.display = "none";
        })
        

        fileimg.addEventListener("click", function()
        {
            hide_emoji();
            if (document.querySelector(".upfile_div").style.display == "none") 
                input_img.click();
            else
                document.querySelector(".upfile_div").style.display = "none";
            });
    </script>

</html>
