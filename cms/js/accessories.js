var ajaxLoaderClass = '';
var brandAccessoriesCount = 0;
var accessoriesListData = {};
var brandsTable;
var brandAccessoriesTable;
var accessoriesTable;
var arBrandsID = [];
var language = {
    search: 'Поиск:',
    loadingRecords: 'Загрузка...',
    emptyTable: 'Записи отсутствуют'
};

/**
 * Действия инициализации страницы
 */
function init() {
    var groupID = $('.brands-groups-list').val();
    ajaxLoaderClass = '.ajax-loader_accessories-choice-box';

    $.ajaxSetup({
        type: 'POST',
        global: true,
        cache: false,
        dataType: 'json',
        url: '../be/accessories_bind.php',
        error: Err
    });

    setGroup(groupID);

    brandsTable = $('.brands-box__table').DataTable({
        "scrollY": "400px",
        "scrollX": true,
        "scrollCollapse": true,
        "paging": false,
        language: language,
        select: true,
        dom: 'Blfrtp',
        ajax: {
            url: '../be/accessories_bind.php',
            'dataSrc': 'data',
            "data": function (d) {
                var params = {};
                params.act = 'getBrandsByGroup';
                params.group = getGroup();

                return params
            }
        },
        autoWidth: false,
        rowId: 'id',
        createdRow: function (row, data, index) {
            $(row).addClass('brands-box__item');
            $(row).attr('data-id', data.id);
            $(row).attr('data-name', data.name);
            $(row).find('td:eq(3)').addClass('brands-box__item-count');
        },
        columns: [
            {
                data: function (row, type, val, meta) {
                    return '<input class="brands-box__checkbox" type="checkbox" name="brands[]" value="' + row.id + '">';
                },
                orderable: false
            },
            {data: "id", "defaultContent": ""},
            {data: "name", "defaultContent": ""},
            {data: "count", "defaultContent": ""}
        ]
    });

    brandAccessoriesTable = $('.brand-accessories-table').DataTable({
        "scrollY": "400px",
        "scrollCollapse": true,
        "paging": false,
        language: {
            search: 'Поиск:',
            loadingRecords: 'Загрузка...',
            emptyTable: 'Список аксессуаров пуст'
        },
        select: true,
        dom: 'Blfrtp',
        autoWidth: false,
        rowId: 'id',
        createdRow: function (row, data, index) {
            $(row).addClass('brand-accessories-box__item');
            $(row).attr('data-id', data.id);
        },
        columns: [
            {data: "id", "defaultContent": ""},
            {data: "name", "defaultContent": ""},
            {
                data: function (row, type, val, meta) {
                    return '<input type="text" name="accessories[' + row.id + '][price]" value="' + row.price + '">';
                },
                orderable: false
            },
            {
                data: function (row, type, val, meta) {
                    return '<button class="remove-accessory" title="Удалить"></button>';
                },
                orderable: false
            }
        ]
    });

    accessoriesTable = $('.accessories-box__table').DataTable({
        "scrollY": "400px",
        "scrollCollapse": true,
        "paging": false,
        select: true,
        language: language,
        dom: 'Blfrtp',
        ajax: {
            url: '../be/accessories_bind.php',
            'dataSrc': 'data',
            "data": function (d) {
                var params = {};
                params.act = 'getAccessoriesByGroup';
                params.group = getGroup();

                return params
            }
        },
        autoWidth: false,
        rowId: 'id',
        createdRow: function (row, data, index) {
            $(row).addClass('accessories-box__item');
            $(row).attr('data-id', data.id);
            $(row).attr('data-name', data.name);
            $(row).attr('data-price', data.price);
        },
        columns: [
            {data: "id", "defaultContent": ""},
            {data: "name", "defaultContent": ""},
            {data: "price", "defaultContent": ""}
        ]
    });
}

/**
 * Записывает ID выбранной группы товаров в поле формы
 */
function setGroup(groupID) {
    $('[name = group]').val(groupID);
}

/**
 * Возвращает ID выбранной группы товаров
 */
function getGroup() {
    return $('[name = group]').val();
}

/**
 * Создаёт список брендов по ID группы товаров
 * @param group - ID группы товаров
 */
function getBrandsByGroup(group) {
    ajaxLoaderClass = '.ajax-loader_accessories-choice-box';

    $.ajax({
        data: {'act': 'getBrandsByGroup', 'group': group},
        success: function (res) {
            $('.brands-box__list').html(res.data);
            brandsTable.clear();
            brandsTable.rows.add(res.data).draw();
        }
    });
}

/**
 * Создаёт список аксессуаров по ID группы товаров
 * @param group - ID группы товаров
 */
function getAccessoriesByGroup(group) {
    $.ajax({
        data: {'act': 'getAccessoriesByGroup', 'group': group},
        success: function (res) {
            accessoriesTable.clear();
            accessoriesTable.rows.add(res.data).draw();
        }
    });
}

/**
 * Создаёт список аксессуаров выбранного бренда
 * @param group - ID группы товаров
 * @param brandsID - ID бренда товаров
 * @param brandName - имя бренда товаров (для вывода текущего бренда)
 */
function getBrandAccessories(group, brandsID, brandName) {
    ajaxLoaderClass = '.ajax-loader_brand-accessories';
    accessoriesListData = {};

    $.ajax({
        data: {'act': 'getBrandAccessories', 'group': group, 'brandsID': brandsID},
        success: function (res) {
            brandAccessoriesTable.clear();
            brandAccessoriesTable.rows.add(res.data).draw();

            if (brandName) {
                $('.accessories-choice-box__brand-name').html('<b>Бренд:</b> ' + brandName);
                brandAccessoriesCount = $('.brand-accessories-box__item').length;
            }

            accessoriesFormDataUpdate();
            checkAccessories();
        }
    });
}

/**
 * Добавляет аксесуар бренду из списка аксесуаров
 * @param id - ID аксессуара
 * @param name - имя аксессуара
 * @param price - цена аксессуара по умолчанию
 */
function addAccessory(id, name, price) {
    var elementExist = false;

    $('.brand-accessories-box__item').each(function (index, element) {
        if ($(element).data('id') === id) {
            elementExist = true;
            return true;
        }
    });

    if (!elementExist) {
        $('.brand-accessories-box__message').html('');
        var data = {
            id: id,
            name: name,
            price: price
        };

        brandAccessoriesTable.row.add(data).draw();
    }
}

/**
 * Сохраняет аксессуары бренда
 * @param brandsID - массив с ID брэндов
 * @param formData - данные формы аксессуаров бренда
 */
function saveAccessories(brandsID, formData) {
    ajaxLoaderClass = '.ajax-loader_save';
    $('.accessories-choice-box__save-result').html('');

    $.ajax({
        data: {'act': 'saveAccessories', 'brandsID': brandsID, 'formData': formData},
        success: function (res) {
            $('.accessories-choice-box__save-result').html(res.data).fadeIn();

            if ($('.brand-accessories-box__item').length !== 0) {
                brandsID.forEach(function (item) {
                    $('[data-id =' + item + '].brands-box__item').children('.brands-box__item-count').html($('.brand-accessories-box__item').length);
                });
            } else {
                brandsID.forEach(function (item) {
                    $('[data-id =' + item + '].brands-box__item').children('.brands-box__item-count').html('Нет');
                });
            }

            brandAccessoriesCount = $('.brand-accessories-box__item').length;
            accessoriesFormDataUpdate();
            setTimeout(hideSaveStatus, 1000);
        }
    });
}

/**
 * Проверяет выбранные аксессуары бренда
 */
function checkAccessories() {
    $('.accessories-box__item').removeClass('accessories-box__item_added');
    $('.accessories-box__item').each(function (acIndex, accessory) {
        $('.brand-accessories-box__item').each(function (bAcIndex, brandAccessory) {
            if ($(accessory).data('id') === $(brandAccessory).data('id')) {
                $(accessory).addClass('accessories-box__item_added');
            }
        });
    });
}

/**
 * Удаляет сообщение под списком аксессуаров бренда
 */
function removeMessage() {
    $('.brand-accessories-box__message').html('');
}

/**
 * Сохраняет данные формы
 */
function accessoriesFormDataUpdate() {
    accessoriesListData = $(".brand-accessories-form").serialize();
}

/**
 * Убирает статус сохоанения
 */
function hideSaveStatus() {
    $('.accessories-choice-box__save-result').fadeOut(600);
}

// Обработчики событий
$().ready(function () {
    /**
     * Обработчик выбора группы товаров
     */
    $('.brands-groups-list').on('change', function () {
        var group = $(this).val();

        if (group) {
            getBrandsByGroup(group);
            getAccessoriesByGroup(group);
            setGroup(group);
            brandAccessoriesCount = 0;
            accessoriesListData = {};
            brandAccessoriesTable.clear();
            brandAccessoriesTable.draw();
        }
    });

    /**
     * Обработчик выбора бренда
     */
    $('.brands-box__list').on('click', 'tr', function () {
        var brandsID = [];
        var brandName = '';
        var saveChange = false;
        var checkBoxActive = false;
        var selectedBrandsID = $('.brands-box__checkbox:checked').map(function () {
            checkBoxActive = true;
            return this.value;
        }).get();

        if (checkBoxActive) {
            brandsID = selectedBrandsID;
            brandName = 'Группа брендов';
            saveChange = false;
        } else {
            brandsID = [$(this).data('id')];
            brandName = $(this).data('name');
            saveChange = false;
        }

        if (brandsID) {
            if (brandAccessoriesCount !== $('.brand-accessories-box__item').length) {
                saveChange = confirm('Изменения не сохранены. Сохранить?');
            } else if (accessoriesListData.length && accessoriesListData !== $(".brand-accessories-form").serialize()) {
                saveChange = confirm('Изменения не сохранены. Сохранить?');
            }

            if (saveChange) {
                saveAccessories($(".brand-accessories-form").serialize());
            } else {
                getBrandAccessories(getGroup(), brandsID, brandName);
                arBrandsID = brandsID;
                // setBrandID(brandsID);

                $('.brands-box__item').removeClass('brands-box__item_selected');

                brandsID.forEach(function (item) {
                    $('.brands-box__item[data-id="' + item + '"]').addClass('brands-box__item_selected');
                });
            }
        }
    });

    /**
     * Обработчик добавления аксессуара бренду
     */
    $('.accessories-box__list').on('click', '.accessories-box__item', function () {
        var ID = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');

        removeMessage();
        addAccessory(ID, name, price);
        checkAccessories();
    });

    /**
     * Обработчик удаления аксессуара бренда
     */
    $('.brand-accessories-box__list').on('click', '.remove-accessory', function () {
        event.preventDefault();
        $(this).closest('.brand-accessories-box__item').remove();
        checkAccessories();
    });

    /**
     * Обработчик сохранения аксессуаров бренда
     */
    $('.accessories-choice-box__save').on('click', function () {
        event.preventDefault();
        accessoriesFormDataUpdate();

        console.log(arBrandsID);
        console.log(arBrandsID.length);

        if (arBrandsID.length === 0) {
            $('.accessories-choice-box__save-result').html('Бренд не выбран').fadeIn();
            setTimeout(hideSaveStatus, 1000);
        } else saveAccessories(arBrandsID, accessoriesListData);
    });

    init();
});

$(document).ajaxStart(function () {
    $(ajaxLoaderClass).show();
}).ajaxStop(function () {
    $(ajaxLoaderClass).hide();
});