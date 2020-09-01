<?php
namespace frontend\controllers;

use common\models\Comment;
use common\models\Ticket;
use common\models\User;
use common\models\CommentForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\TicketForm;
use frontend\models\UserForm;
use frontend\models\VerifyEmailForm;
use phpDocumentor\Reflection\Types\Integer;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;

use yii\web\UploadedFile;

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
                'only' => ['logout', 'signup', 'show-ticket', 'close-ticket', 'update-profile', 'show-user-tickets', 'show-profile', 'add-ticket'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'show-ticket', 'close-ticket', 'update-profile', 'show-user-tickets', 'show-profile', 'add-ticket'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    protected function getUserId()
    {
        return Yii::$app->user->identity->getId();
    }

    protected function getUserTicket($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::find()
            ->ofId($id)
            ->ofUserId($this->getUserId())
            ->one();

        if ($ticket == null) {
            return $this->goHome();
        }

        return $ticket;
    }

    protected function getUser()
    {
        $user = User::find()
            ->ofId($this->getUserId())
            ->one();

        return $user;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        $a = 22;
        $b = ["egy", "ketto", "harom"];

        return $this->render('about', [
            'a' => $a,
            'b' => $b,
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    //new code

    /**
     * Add a Ticket
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionAddTicket()
    {
        $ticketForm = new TicketForm();

        if (Yii::$app->request->isPost  && $ticketForm->load($_POST)) {
            $ticketForm->imageFiles = UploadedFile::getInstances($ticketForm, 'imageFiles');

            if ($ticketForm->validate()) {
                $ticket = new Ticket();
                $ticket->user_id = $this->getUserId();
                $ticket = $ticketForm->fillTo($ticket);

                if ($ticket->save()) {
                    $images = $ticketForm->fillImages($ticket->id);

                    foreach ($images as $image) {
                        try {
                            $image->save();
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage(), $e->errorInfo);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'You have added this ticket: ' . $ticketForm->title);

                    $folderRead = Yii::getAlias('@frontend/web/uploads/');
                    $folderWrite = Yii::getAlias('@backend/web/uploads/');
                    $uploadCopy = opendir($folderRead);

                    while ($file = readdir($uploadCopy)) {
                        if ($file != "." && $file != ".." && $file != ".gitkeep") {
                            copy($folderRead . $file, $folderWrite . $file);
                        }
                    }

                    closedir($uploadCopy);

                    return $this->redirect('/site/show-user-tickets');
                } else {
                    Yii::$app->session->setFlash('error', 'Something went wrong.');
                }
            }
        }

        return $this->render('add_ticket', [
            'ticketForm' => $ticketForm,
        ]);
    }

    /**
     * Displays the logged in user's profile
     *
     * @return string
     */
    public function actionShowProfile()
    {
            $user = $this->getUser();

            return $this->render('show_profile', [
                'user' => $user,
            ]);
    }

    /**
     * Lists the logged in user's tickets
     *
     * @return string
     */
    public function actionShowUserTickets()
    {
        $tickets = Ticket::find()
            ->ofUserId($this->getUserId())
            ->with('comments')
            ->orderBy([
                'is_open' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render('show_user_tickets', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Updates the logged in user's profile
     *
     * @return string
     * @throws Exception
     */
    public function actionUpdateProfile()
    {
        $user = $this->getUser();

        $userForm = new UserForm();
        $userForm->fillFrom($user);

        if ($userForm->load(Yii::$app->request->post())) {
            if ( $userForm->validate() && $user->validatePassword($userForm->old_password)) {
                $user = $userForm->fillTo($user);

                if ($user->save()) {
                    Yii::$app->session->setFlash('success', 'You have updated the profile.');
                }

            } else {
                Yii::$app->session->setFlash('error', 'Failed to save');
            }
        }

        return $this->render('update_profile', [
            'userForm' => $userForm,
            'user' => $user,
        ]);
    }

    /**
     * Displays the logged in user's ticket
     *
     * @param $id integer ticket's ID
     * @return string|Response
     * @throws Exception
     */
    public function actionShowTicket($id)
    {
        $ticket = $this->getUserTicket($id);

        $transaction = Yii::$app->db->beginTransaction();
        $request = Yii::$app->request->post();
        $commentForm = new CommentForm();

        if ($commentForm->load($request) && $commentForm->validate()) {
            $comment = new Comment();
            $comment = $commentForm->fillTo($comment);
            $comment->user_id = $this->getUserId();
            $comment->ticket_id = $id;

            if (!$ticket->is_open) {
                $ticket->is_open = true;
            }

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

        return $this->render('show_ticket', [
            'commentForm' => $commentForm,
            'ticket' => $ticket,
            'comments' => $comments,
            'images' => $images,
        ]);
    }

    /**
     * Closes a specific ticket
     *
     * @param $id integer the ticket's ID
     * @return false|Response
     */
    public function actionCloseTicket($id)
    {
        $ticket = $this->getUserTicket($id);

        if ($ticket->is_open) {
            $ticket->is_open = false;
        }

        if ($ticket->save()) {
            Yii::$app->session->setFlash('success', 'You have closed this ticket: ' . $ticket->title);

            $this->redirect('/site/show-user-tickets');
        } else {
            Yii::$app->session->setFlash('error', 'Ticket close failed.');

            return $this->goHome();
        }

        return false;
    }
}
