<?php
namespace algiadesign\moneyalgia\provider;

use algiadesign\moneyalgia\MoneyalgiaPlugin;

/**
 * データ保存
 *
 * @author deceitya
 */
interface Provider
{
    /**
     * コンストラクタ
     *
     * @param MoneyalgiaPlugin プラグイン
     */
    public function __construct(MoneyalgiaPlugin $plugin);

    /**
     * データベースを開く
     *
     * @return boolean 開けたらtrue、それ以外はfalse
     */
    public function open(): bool;

    /**
     * アカウントが存在しているか
     *
     * @param string $id ユーザー名
     * @return boolean 存在していればtrue、それ以外はfalse
     */
    public function accountExists(string $id): bool;

    /**
     * アカウント作成
     *
     * @param string  $id     ユーザー名
     * @param integer $amount 初期値
     * @return boolean 作成成功でtrue、それ以外はfalse
     */
    public function createAccount(string $id, int $amount): bool;

    /**
     * アカウント削除
     *
     * @param string $id ユーザー名
     * @return boolean 削除成功でtrue、それ以外はfalse
     */
    public function removeAccount(string $id): bool;

    /**
     * ユーザーが所持しているお金の量を取得
     *
     * @param string $id ユーザー名
     * @return integer|null お金の量、アカウントが存在しない場合null
     */
    public function getAmount(string $id): ?int;

    /**
     * ユーザーが所持するお金の量を変更
     *
     * @param string  $id     ユーザー名
     * @param integer $amount お金の量
     * @return boolean 変更成功でtrue、それ以外はfalse
     */
    public function setAmount(string $id, int $amount): bool;

    /**
     * 全ユーザーの所持金の量を配列で返す
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * セーブ
     *
     * @return boolean セーブ成功でtrue、それ以外はfalse
     */
    public function save(): bool;

    /**
     * データベース閉じる
     *
     * @return boolean 成功でtrue、それ以外はfalse
     */
    public function close(): bool;
}
