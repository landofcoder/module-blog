<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Blog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableItems = $installer->getTable('ves_blog_category');
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $installer->getConnection()->addColumn(
                $tableItems,
                'parent_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Parent Id'
                ]
            );


            $installer->getConnection()->addColumn(
                $tableItems,
                'posts_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Style'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'posts_template',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Template'
                ]
            );


            $postTable = $installer->getTable('ves_blog_post');

            $installer->getConnection()->addColumn(
                $postTable,
                'is_private',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment'  => 'Is Private'
                ]
            );

            $installer->getConnection()->addColumn(
                $postTable,
                'like',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment'  => 'Like'
                ]
            );

            $installer->getConnection()->addColumn(
                $postTable,
                'disklike',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment'  => 'Disk Like'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'post_template',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Post Template'
                ]
            );


            /**
             * Create table 'ves_blog_products_related'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_post_products_related')
            )->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Post ID'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Position'
            )->addForeignKey(
                $installer->getFkName('ves_blog_post_products_related', 'post_id', 'ves_blog_post', 'post_id'),
                'post_id',
                $installer->getTable('ves_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                    $installer->getFkName('ves_blog_post_products_related_entity_id', 'entity_id', 'catalog_product_entity', 'entity_id'),
                    'entity_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
            ->setComment('Post To Product Linkage Table');
            $installer->getConnection()->createTable($table);


            /**
             * Create table 'ves_blog_post_author'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_post_author')
            )->addColumn(
                'author_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Author Id'
            )->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )->addColumn(
                'user_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )->addColumn(
                'nick_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Nick Name'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Description'
            )->addColumn(
                'avatar',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Avatar'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'User Id'
            )->addColumn(
                'page_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Page Title'
            )->addColumn(
                'meta_keywords',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Meta Keywords'
            )->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Meta Description'
            )->addColumn(
                'social_networks',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Social Networks'
            )->setComment('Post Author');
            $installer->getConnection()->createTable($table);


            /**
             * Create table 'ves_blog_post_vote'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_post_vote')
            )->addColumn(
                'vote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Vote Id'
            )->addColumn(
                'like',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Like'
            )->addColumn(
                'disklike',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Disk Like'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer IP'
            )->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Post Id'
            )->addIndex(
                $installer->getIdxName('ves_blog_post_vote', ['vote_id','post_id']),
                ['vote_id']
            )->addForeignKey(
                $installer->getFkName('ves_blog_post_vote', 'post_id', 'ves_blog_post', 'post_id'),
                'post_id',
                $installer->getTable('ves_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Vote'
            );
            $installer->getConnection()->createTable($table);

        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $table = $installer->getTable('ves_blog_comment');

            $installer->getConnection()->addColumn(
                $table,
                'parent_id',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'   => 10,
                    'nullable' => false,
                    'default'  => 0,
                    'comment'  => 'Parent Comment Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $table = $installer->getTable('ves_blog_post_author');

            $installer->getConnection()->addColumn(
                $table,
                'is_view',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'   => 10,
                    'nullable' => false,
                    'default'  => 1,
                    'comment'  => 'Is view author info on frontend'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'creation_time',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    'comment'  => 'Author Creation Time'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'update_time',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    'comment'  => 'Author Modification Time'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $table = $installer->getTable('ves_blog_post_tag');
            $installer->getConnection()->addColumn(
                $installer->getTable($table),
                'meta_robots',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Tag Degault Robots',
                ]
            );

            $tablePost = $installer->getTable('ves_blog_post');
            $installer->getConnection()->addColumn(
                $tablePost,
                'meta_title',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Meta Title'
                ]
            );
            $installer->getConnection()->addColumn(
                $tablePost,
                'og_metadata',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Og Metadata'
                ]
            );
            $installer->getConnection()->addColumn(
                $tablePost,
                'og_title',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Og Title'
                ]
            );
            $installer->getConnection()->addColumn(
                $tablePost,
                'og_description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Og Description'
                ]
            );
            $installer->getConnection()->addColumn(
                $tablePost,
                'og_img',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Og Img'
                ]
            );
            $installer->getConnection()->addColumn(
                $tablePost,
                'og_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Posts Og type, leave empty to use default article'
                ]
            );
            
        }
        if (version_compare($context->getVersion(), '1.0.8', '<')) {

            $tablePost = $installer->getTable('ves_blog_post');
            $installer->getConnection()->addColumn(
                $tablePost,
                'real_post_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Real Post Url'
                ]
            );

            $installer->getConnection()->addColumn(
                $tablePost,
                'real_image_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Real Image Url'
                ]
            );

            $installer->getConnection()->addColumn(
                $tablePost,
                'real_thumbnail_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Real Thumbnail Url'
                ]
            );

            $tableFeed = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_feed')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Feed title'
            )->addColumn(
                'feed_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Name'
            )
            ->addColumn(
                'cronjob_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Cronjob Time'
            )
            ->addColumn(
                'creation_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Converter Creation Time'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default'=>1],
                'Is Active'
            )
            ->addColumn(
                'settings',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'settings'
            )
            ->addColumn(
                'convert_data_fields',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Convert Data Fields'
            )
            ->setComment('Blog feed link Table');
            $installer->getConnection()->createTable($tableFeed);
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {

            $tableImporter = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_importer')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Feed title'
            )->addColumn(
                'default_author_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Default Author ID'
            )->addColumn(
                'default_store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Default Store ID'
            )->addColumn(
                'blog_base_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Blog Base Url'
            )->addColumn(
                'dbhost',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Database Host'
            )
            ->addColumn(
                'dbname',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Database Name'
            )
            ->addColumn(
                'uname',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Database User Name'
            )
            ->addColumn(
                'pwd',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Database User Password'
            )
            ->addColumn(
                'prefix',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true,'default' => 'wp_'],
                'Database Table Prefix'
            )
            ->addColumn(
                'connect_charset',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true,'default' => 'utf8mb4'],
                'Database Default Connect Charset'
            )
            ->addColumn(
                'import_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false, 'default'=> 'wordpress'],
                'Import type'
            )
            ->addColumn(
                'cronjob_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Cronjob Time'
            )
            ->addColumn(
                'creation_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Importer Creation Time'
            )->addColumn(
                'last_import_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Last Import Time'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Is Active'
            )
            ->addColumn(
                'convert_image_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Is Convert Image Link'
            )
            ->addColumn(
                'overwrite',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Is Override'
            )
            ->addColumn(
                'import_posts',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Is Import Posts'
            )
            ->addColumn(
                'import_tags',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Is Import Tags'
            )
            ->addColumn(
                'import_categories',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Is Import Categories'
            )
            ->addColumn(
                'import_comments',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                'Is Import Comments'
            )
            ->addColumn(
                'settings',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'settings'
            )
            ->addColumn(
                'convert_data_fields',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Convert Data Fields'
            )
            ->setComment('Blog importer link Table');
            $installer->getConnection()->createTable($tableImporter);
        }
        if (version_compare($context->getVersion(), '1.0.10', '<')) {

            $tableImporterLog = $installer->getConnection()->newTable(
                $installer->getTable('ves_blog_importer_log')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'importer_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Importer title'
            )
            ->addColumn(
                'importer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default'=>0],
                'Importer ID'
            )
            ->addColumn(
                'log_message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Convert Data Fields'
            )->addColumn(
                'creation_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Importer Creation Time'
            )
            ->setComment('Blog importer log Table');
            $installer->getConnection()->createTable($tableImporterLog);
        }
        $installer->endSetup();
    }
}