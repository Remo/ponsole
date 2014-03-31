<?php

defined('C5_EXECUTE') or die('Access Denied.');

class $BLOCK_CLASS$BlockController extends BlockController {

    protected $btTable = '$BLOCK_TABLE$';
    protected $btInterfaceWidth = '600';
    protected $btInterfaceHeight = '465';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;

    public function getBlockTypeName() {
        return t('$BLOCK_NAME$');
    }

    public function getBlockTypeDescription() {
        return t('$BLOCK_DESCRIPTION$');
    }

}
