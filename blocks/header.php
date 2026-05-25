    <body>
        <!--<div class ="header">-->
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="index.php"><img src="images/logo.png" width="125px"></a>
                </div>
                 <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                         <li><a href="products.php">Товары</a></li>
                         <li><a href="about.php">О нас</a></li>
                         <li><a href="">Контакты</a></li>

                         <?php 
                            if(isset($_POST['login']) ) {
                                echo'<li><a href="/user.php"Кабинет пользователя</a></li>';

                            }
                            else{
                                echo '<li><a href="user.php">Аккаунт</a></li>';
                            }
                         
                         ?>
                    </ul>
                </nav>
            </div>
        </div>
    </body>