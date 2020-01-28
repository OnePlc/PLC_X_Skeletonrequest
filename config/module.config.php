<?php
/**
 * module.config.php - Skeletonrequest Config
 *
 * Main Config File for Skeletonrequest Module
 *
 * @category Config
 * @package Skeletonrequest
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

namespace OnePlace\Skeletonrequest;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    # Skeletonrequest Module - Routes
    'router' => [
        'routes' => [
            # Module Basic Route
            'skeletonrequest' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/skeletonrequest[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SkeletonrequestController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'skeletonrequest-api' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/skeletonrequest/api[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    # View Settings
    'view_manager' => [
        'template_path_stack' => [
            'skeletonrequest' => __DIR__ . '/../view',
        ],
    ],
];
