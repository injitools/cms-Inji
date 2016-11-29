<form action = '' method = 'POST' enctype="multipart/form-data">
    <div class ='row'>  
        <div class ='col-xs-6'>
            <div class="form-group">
                <label>Имя*</label>
                <input type = 'text' name = 'Callbacks[name]' class = 'form-control' required/>
            </div>
            <div class="form-group">
                <label>Тип отзыва</label>
                <select name = 'Callbacks[type]'  class = 'form-control'><option value = 1>Положительный</option><option value = 2>Нейтральный</option><option value = 3>Отрицательный</option></select>
            </div>
            <div class="form-group">
                <label>Номер телефона для обратной связи* <small>(виден только директору)</small></label>
                <input type = 'text' name = 'Callbacks[phone]' class = 'form-control' required/>
            </div>
        </div>
        <div class ='col-xs-6'>
            <div class="form-group">
                <label>E-mail*</label>
                <input type = 'text' name = 'Callbacks[mail]' class = 'form-control' required/>
            </div>
            <div class="form-group">
                <label>Ваша фотография <small>(По вашему желанию)</small></label>
                <input type = 'file' name = 'Callbacks[photo]'/>
            </div>
        </div>
        <div class ='col-xs-12 text-center'>      
            <div class="form-group">
                <label >Отзыв*</label>
                <textarea name = 'Callbacks[text]' rows = 9  class = 'form-control' required></textarea>
            </div>
            <button></button>
        </div>
    </div>
</form>