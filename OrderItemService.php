<?php

namespace App\Order;

use App\Admin\Frontend\Importer\OrderItemToSave;
use App\Admin\Frontend\Importer\OrderToSave;
use App\Currency\ICurrencyService;
use App\Good\Attribute\IGoodAttributeService;
use App\Good\GoodNameUtils;
use App\Good\IGoodProvider;
use App\Localization\Language;
use App\Pricing\IPricingService;
use App\Search\ISearchProvider;

/**
 * Сервис управления позициями заказа.
 */
class OrderItemService implements IOrderItemService
{
    /**
     * @var IOrderItemProvider
     */
    private $orderItemProvider;
    /**
     * @var ISearchProvider
     */
    private $searchProvider;
    /**
     * @var IGoodAttributeService
     */
    private $goodsAttributeService;
    /**
     * @var IPricingService
     */
    private $pricingService;
    /**
     * @var IGoodProvider
     */
    private $goodProvider;
    /**
     * @var ICurrencyService
     */
    private $currencyService;

    /**
     * Метод возвращает значение.
     *
     * @return IOrderItemProvider
     */
    public function getOrderItemProvider(): IOrderItemProvider
    {
        return $this->orderItemProvider;
    }

    /**
     * Метод возвращает значение.
     *
     * @return ISearchProvider
     */
    public function getSearchProvider(): ISearchProvider
    {
        return $this->searchProvider;
    }

    /**
     * Метод возвращает значение.
     *
     * @return IGoodAttributeService
     */
    public function getGoodsAttributeService(): IGoodAttributeService
    {
        return $this->goodsAttributeService;
    }

    /**
     * Метод возвращает значение.
     *
     * @return IPricingService
     */
    public function getPricingService(): IPricingService
    {
        return $this->pricingService;
    }

    /**
     * Метод возвращает значение.
     *
     * @return IGoodProvider
     */
    public function getGoodProvider(): IGoodProvider
    {
        return $this->goodProvider;
    }

    /**
     * Метод возвращает значение.
     *
     * @return ICurrencyService
     */
    public function getCurrencyService(): ICurrencyService
    {
        return $this->currencyService;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param IOrderItemProvider $value Новое значение.
     *
     * @return IOrderItemService
     */
    public function setOrderItemProvider(IOrderItemProvider $value): IOrderItemService
    {
        $this->orderItemProvider = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param ISearchProvider $value
     *
     * @return IOrderItemService
     */
    public function setSearchProvider(ISearchProvider $value): IOrderItemService
    {
        $this->searchProvider = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param IGoodAttributeService $value
     *
     * @return IOrderItemService
     */
    public function setGoodAttributeService(IGoodAttributeService $value): IOrderItemService
    {
        $this->goodsAttributeService = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param IPricingService $value
     *
     * @return IOrderItemService
     */
    public function setPricingService(IPricingService $value): IOrderItemService
    {
        $this->pricingService = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param IGoodProvider $value
     *
     * @return IOrderItemService
     */
    public function setGoodProvider(IGoodProvider $value): IOrderItemService
    {
        $this->goodProvider = $value;
        return $this;
    }

    /**
     * Метод устанавливает значение.
     *
     * @param ICurrencyService $value
     *
     * @return IOrderItemService
     */
    public function setCurrencyService(ICurrencyService $value): IOrderItemService
    {
        $this->currencyService = $value;
        return $this;
    }

    /**
     * @param IOrderItemProvider    $orderItemProvider
     * @param ISearchProvider       $searchProvider
     * @param IGoodAttributeService $goodsAttributeService
     * @param IPricingService       $pricingService
     * @param IGoodProvider         $goodProvider
     * @param ICurrencyService      $currencyService
     */
    public function __construct(
        IOrderItemProvider $orderItemProvider,
        ISearchProvider $searchProvider,
        IGoodAttributeService $goodsAttributeService,
        IPricingService $pricingService,
        IGoodProvider $goodProvider,
        ICurrencyService $currencyService
    )
    {
        $this->setOrderItemProvider($orderItemProvider);
        $this->setSearchProvider($searchProvider);
        $this->setGoodAttributeService($goodsAttributeService);
        $this->setPricingService($pricingService);
        $this->setGoodProvider($goodProvider);
        $this->setCurrencyService($currencyService);
    }

    /**
     * Добавит позицию в заказ.
     *
     * @inheritdoc
     */
    public function addItemToOrder(Order $order, $goodPriceId, $quantity, $price, $lang): void
    {
        $orderItem = OrderItem::build()
                              ->assignFrom($this->buildOrderItemByGoodPriceId($order, $goodPriceId, $quantity, $lang))
                              ->setPrice($price)
                              ->create();

        $this->getOrderItemProvider()->createNewOrderItem($orderItem);
    }

    /**
     * Обновляет данные позиции в заказе.
     *
     * @param OrderItemToSave $itemToSave
     * @param OrderToSave     $orderToSave
     *
     * @return void
     */
    public function updateItem(OrderItemToSave $itemToSave, OrderToSave $orderToSave): void
    {
        if (! $itemToSave->getId()) {
            throw new \LogicException('No ID is given');
        }

        $currentOrderItem = $this->getOrderItemProvider()->findOrderItemById($itemToSave->getId());
        if (! $currentOrderItem) {
            throw new \LogicException('Order item was not found');
        }

        $orderItem = $this->getItemForUpdate($itemToSave, $currentOrderItem);

        $this->getOrderItemProvider()->updateItem($orderItem);
    }

    /**
     * Возвращает заказ для обновления данных.
     *
     * @param OrderItemToSave $itemToSave
     * @param mixed           $currentOrderItem
     *
     * @return OrderItem
     */
    protected function getItemForUpdate(OrderItemToSave $itemToSave, $currentOrderItem): OrderItem
    {
        return OrderItem::build()
                        ->assignFrom($currentOrderItem)
                        ->setStatusId($itemToSave->getStatusId())
                        ->setPrice($itemToSave->getPrice())
                        ->setQuantityFinal($itemToSave->getQuantity())
                        ->setReplacementGoodId($itemToSave->getReplacementGoodId())
                        ->setCalcWeight($itemToSave->getWeightCalc())
                        ->create();
    }

    /**
     * Билдит объект OrderItem по goodPriceId.
     *
     * @inheritdoc
     */
    public function buildOrderItemByGoodPriceId(Order $order, $goodPriceId, $quantity, $lang): OrderItem
    {
        $goodsData = $this->getSearchProvider()->getDetailGoodsPricesData([$goodPriceId], $lang);
        $data      = $goodsData ? reset($goodsData) : null;

        $price                = 0;
        $priceWithoutDiscount = 0;
        try {
            $prices = $this->getPricingService()->getGoodsPricesWithDiscounts(
                [$goodPriceId],
                $order->getCurrencyId(),
                $order->getCustomerId(),
                $order->getShippingAddress() ? $order->getShippingAddress()->getRegionId() : 0
            );

            list($price, $priceWithoutDiscount) = reset($prices);
        } catch (\Exception $e) {
        }

        $goodAttributes = $this->getGoodsAttributeService()->getAttributes($data->good_id, $lang);
        $titleEn        = GoodNameUtils::getGoodName($data->name_en, $goodAttributes);
        $titleRu        = GoodNameUtils::getGoodName($data->name_ru, $goodAttributes);
        $title          = ($lang == Language::RUSSIAN && $titleRu) ? $titleRu : $titleEn;
        $name           = $this->getGoodProvider()
                               ->getGoodManufacturerNameById($data->good_id) . ' ' . $data->catalog_num . ($title ? ' - ' . $title : '');

        return $this->getItemByGoodPriceId($data, $name, $price, $priceWithoutDiscount, $order, $quantity, $goodPriceId);
    }

    /**
     * Возвращает заказ построенный по goodPriceId.
     *
     * @param array  $data
     * @param string $name
     * @param int    $price
     * @param int    $priceWithoutDiscount
     * @param Order  $order
     * @param int    $quantity
     * @param int    $goodPriceId
     *
     * @return OrderItem
     */
    protected function getItemByGoodPriceId($data, $name, $price, $priceWithoutDiscount, $order, $quantity, $goodPriceId): OrderItem
    {
        $data = reset($data);
        return OrderItem::build()
                        ->setGoodId($data->good_id)
                        ->setPartNumber($data->catalog_num)
                        ->setName($name)
                        ->setPrice($price)
                        ->setPriceNoDiscount($priceWithoutDiscount)
                        ->setSiteId($data->site_id)
                        ->setCustomerId($order->getCustomerId())
                        ->setQuantityInit($quantity)
                        ->setQuantityFinal($quantity)
                        ->setDeliveryId($data->delivery_id)
                        ->setOrderId($order->getId())
                        ->setOrderSeqId($order->getSeqId())
                        ->setStatusId($order->getStatusId())
                        ->setGoodPriceId($goodPriceId)
                        ->create();
    }

    /**
     * Считает итоговую сумму заказа.
     *
     * @param OrderItemToSave[] $saveItemList
     * @param int               $currencyId
     *
     * @return float
     */
    public function countTotalBySaveItemList($saveItemList, $currencyId): float
    {
        if (empty($saveItemList)) {
            return 0.0;
        }

        $total = 0;
        foreach ($saveItemList as $saveItem) {
            if ($saveItem->isCanceled()) {
                continue;
            }
            $total += $saveItem->getPrice() * $saveItem->getQuantity();
        }

        return $this->getCurrencyService()->roundCurrency($total, $currencyId);
    }
}
