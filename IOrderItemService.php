<?php

namespace App\Order;

use App\Admin\Frontend\Importer\OrderItemToSave;
use App\Admin\Frontend\Importer\OrderToSave;
use App\Currency\ICurrencyService;
use App\Good\Attribute\IGoodAttributeService;
use App\Good\IGoodProvider;
use App\Pricing\IPricingService;
use App\Search\ISearchProvider;

/**
 * Сервис управления позициями заказа.
 */
interface IOrderItemService
{
    /**
     * Добавит позицию в заказ.
     *
     * @param Order  $order
     * @param int    $goodPriceId
     * @param int    $quantity
     * @param float  $price
     * @param string $lang
     *
     * @return void
     */
    public function addItemToOrder(Order $order, $goodPriceId, $quantity, $price, $lang): void;

    /**
     * Обновляет данные позиции в заказе.
     *
     * @param OrderItemToSave $itemToSave
     * @param OrderToSave     $orderToSave
     *
     * @return void
     */
    public function updateItem(OrderItemToSave $itemToSave, OrderToSave $orderToSave): void;

    /**
     * Билдит объект OrderItem по goodPriceId.
     *
     * @param Order  $order
     * @param int    $goodPriceId
     * @param int    $quantity
     * @param string $lang
     *
     * @return OrderItem
     */
    public function buildOrderItemByGoodPriceId(Order $order, $goodPriceId, $quantity, $lang): OrderItem;

    /**
     * Считает итоговую сумму заказа.
     *
     * @param OrderItemToSave[] $saveItemList
     * @param int               $currencyId
     *
     * @return float
     */
    public function countTotalBySaveItemList($saveItemList, $currencyId): float;

    /**
     * Метод возвращает значение.
     *
     * @return IOrderItemProvider
     */
    public function getOrderItemProvider(): IOrderItemProvider;

    /**
     * Метод возвращает значение.
     *
     * @return ISearchProvider
     */
    public function getSearchProvider(): ISearchProvider;

    /**
     * Метод возвращает значение.
     *
     * @return IGoodAttributeService
     */
    public function getGoodsAttributeService(): IGoodAttributeService;

    /**
     * Метод возвращает значение.
     *
     * @return IPricingService
     */
    public function getPricingService(): IPricingService;

    /**
     * Метод возвращает значение.
     *
     * @return IGoodProvider
     */
    public function getGoodProvider(): IGoodProvider;

    /**
     * Метод возвращает значение.
     *
     * @return ICurrencyService
     */
    public function getCurrencyService(): ICurrencyService;

    /**
     * Метод устанавливает значение.
     *
     * @param IOrderItemProvider $value Новое значение.
     *
     * @return IOrderItemService
     */
    public function setOrderItemProvider(IOrderItemProvider $value): IOrderItemService;

    /**
     * Метод устанавливает значение.
     *
     * @param ISearchProvider $value
     *
     * @return IOrderItemService
     */
    public function setSearchProvider(ISearchProvider $value): IOrderItemService;

    /**
     * Метод устанавливает значение.
     *
     * @param IGoodAttributeService $value
     *
     * @return IOrderItemService
     */
    public function setGoodAttributeService(IGoodAttributeService $value): IOrderItemService;

    /**
     * Метод устанавливает значение.
     *
     * @param IPricingService $value
     *
     * @return IOrderItemService
     */
    public function setPricingService(IPricingService $value): IOrderItemService;

    /**
     * Метод устанавливает значение.
     *
     * @param IGoodProvider $value
     *
     * @return IOrderItemService
     */
    public function setGoodProvider(IGoodProvider $value): IOrderItemService;

    /**
     * Метод устанавливает значение.
     *
     * @param ICurrencyService $value
     *
     * @return IOrderItemService
     */
    public function setCurrencyService(ICurrencyService $value): IOrderItemService;
}
