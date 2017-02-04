<div class ="materials-category">
    <div class="row">
        <div class="col-md-3">
            <div class="content">
              <?php $this->widget('Materials\categoryTree', ['category' => $category]); ?>
            </div>
        </div>
        <div class="col-md-9">
            <div class="content">
                <h2 class ='category-name'><?= $category->name; ?></h2>
                <div class="category-description">
                    <?= Ui\FastEdit::block($category, 'description', null, true); ?>
                </div>
                <?php $this->widget('Materials\category/materialList', ['category' => $category]); ?>
            </div>
        </div>
    </div>
</div>