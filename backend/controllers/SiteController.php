<?php
namespace backend\controllers;

use backend\models\UserForm;
use common\models\Comment;
use common\models\Ticket;
use common\models\User;
use common\models\CommentForm;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout',
                            'index',
                            'list-tickets',
                            'list-users',
                            'show-profile',
                            'delete-user',
                            'update-user',
                            'show-ticket',
                            'close-ticket',
                            'assign-ticket',
                            'open-ticket',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    protected function getUserId()
    {
        return Yii::$app->user->identity->getId();
    }

    protected function getUserById($id)
    {
        $user = User::find()
            ->ofId($id)
            ->one();

        if ($user == null) {
            return $this->goHome();
        }

        return $user;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = User::find()
                ->where(['id' => Yii::$app->user->identity->getId()])
                ->one();

            if (!$user['is_admin']) {
                Yii::$app->user->logout();

                Yii::$app->session->setFlash('error', 'You are not an admin.');

                return $this->goHome();
            } else {
                return $this->goBack();
            }
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionListTickets()
    {
        $tickets = Ticket::find()
            ->with('user', 'comments')
            ->orderBy([
                'is_open' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render('list_tickets', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * List all users
     *
     * @return string
     */
    public function actionListUsers()
    {
        $users = User::find()
            ->orderBy([
                'is_admin' => SORT_DESC,
                'id' => SORT_ASC,
            ])
            ->all();

        return $this->render('list_users', [
            'users' => $users,
        ]);
    }

    /**
     * Show profile
     *
     * @param $id integer the user's ID
     * @return string
     */
    public function actionShowProfile($id)
    {
        $user = $this->getUserById($id);

        $tickets = Ticket::find()
            ->ofUserId($id)
            ->orderBy([
                'is_open' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render('show_profile', [
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Delete a user
     *
     * @param $id integer the user's ID
     * @return false|Response
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteUser($id)
    {
        $user = $this->getUserById($id);

        if ($user->delete()) {
            Yii::$app->session->setFlash('success', 'You have deleted this user: ' . $user->username);

            $this->redirect('/site/list-users');
        } else {
            Yii::$app->session->setFlash('error', 'User delete failed');

            return $this->goHome();
        }

        return false;
    }

    /**
     * Update a specific user
     *
     * @param $id integer the user's ID
     * @return string
     */
    public function actionUpdateUser($id)
    {
        $user = $this->getUserById($id);

        $model = new UserForm();
        $model->fillFrom($user);

        if ($model->load(Yii::$app->request->post())) {
            if ( $model->validate()) {
                $user = $model->fillTo($user);
                $user->save();

                if ($user->is_admin == 0 && $user->id == $this->getUserId()) {
                    Yii::$app->user->logout();

                    return $this->goHome();
                }

                Yii::$app->session->setFlash('success', 'You have updated this user: ' . $user->username);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to save');
            }
        }

        return $this->render('update_user', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Show a specific ticket
     *
     * @param $id integer the ticket's ID
     * @return string|Response
     * @throws Exception
     */
    public function actionShowTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->with('user')
            ->ofId($id)
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        $transaction = Yii::$app->db->beginTransaction();

        $request = Yii::$app->request->post();
        $commentForm = new CommentForm();

        if ($commentForm->load($request) && $commentForm->validate()) {
            $comment = new Comment();
            $comment = $commentForm->fillTo($comment);
            $comment->user_id = $this->getUserId();
            $comment->ticket_id = $id;

            if (!$comment->save()) {
                Yii::error('Failed to save Comment ' . json_encode($comment->getAttributes()), __METHOD__);

                $transaction->rollBack();
            }

            if (!$ticket->save()) {
                Yii::error('Failed to save Ticket ' . json_encode($ticket->getAttributes()), __METHOD__);

                $transaction->rollBack();
            }

            $transaction->commit();
        }

        $comments = $ticket->comments;

        $images = $ticket->images;
        $imagePath = \yii::getAlias('@frontendweb');

        return $this->render('show_ticket', [
            'comment_form' => $commentForm,
            'ticket' => $ticket,
            'comments' => $comments,
            'images' => $images,
            'imagePath' => $imagePath,
        ]);
    }

    /**
     * Close a specific ticket
     *
     * @param $id integer the ticket's ID
     * @return false|Response
     */
    public function actionCloseTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->ofId($id)
            ->ofAdminId($this->getUserId())
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        if ($ticket->is_open) {
            $ticket->is_open = false;
        }

        if ($ticket->save()) {
            Yii::$app->session->setFlash('success', 'You have closed this ticket: ' . $ticket->title);

            $this->redirect('/site/list-tickets');
        } else {
            Yii::$app->session->setFlash('error', 'Ticket close failed.');

            return $this->goHome();
        }

        return false;
    }

    /**
     * Assign a specific ticket to the logged in admin
     *
     * @param $id integer the ticket's id
     * @return false|Response
     */
    public function actionAssignTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->ofId($id)
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        if ($ticket->admin_id == null) {
            $ticket->admin_id = $this->getUserId();
        }

        if ($ticket->save()) {
            Yii::$app->session->setFlash('success', 'You have assigned this ticket: ' . $ticket->title);

            $this->redirect('/site/list-tickets');
        } else {
            Yii::$app->session->setFlash('Error', 'Something went wrong.');

            $this->goHome();
        }

        return false;
    }
}
