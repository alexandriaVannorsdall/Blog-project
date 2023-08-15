<h2><?= esc($title) ?></h2>

<?= session()->getFlashdata('error') ?>
<?= validation_list_errors() ?>

<form action="/article/edit" method="post">
    <?= csrf_field() ?>

    <label for="title">Title</label>
    <label>
        <input type="text" name="title" value="<?= isset($post['Title']) ? esc($post['Title']) : '' ?>"/>
    </label>
    <br>
    <br>

    <label for="body">Text</label>
    <label>
        <textarea name="text" cols="45" rows="4"><?= isset($post['Text']) ? esc($post['Text']) : '' ?></textarea>
    </label>
    <br>
    <br>

    <input type="submit" name="submit" value="Submit">
</form>