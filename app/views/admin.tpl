
<h3>Админ-панель</h3>
<div class="enter"><a href="/">Главная</a> <a id="exit" href="#" onclick="return exit()">Выход</a></div>
<div class="error" id="error"></div>
<div class="login" id="enter">
   <div class="login__input">Логин <input type="text" id="login" /></div>
   <div class="login__input">Пароль <input type="password" id="password" /></div>
   <div class="login__input"><input type="submit" value="Войти" onclick="return enter()"/></div>
</div>
{include show}
<script type="text/javascript" language="JavaScript" src="/js/admin.js"> </script>
<script type="text/javascript" language="JavaScript" src="/js/general.js"> </script>