import AuthLayout from '~/components/Layouts/Auth';
import RegisterForm from '../../../components/RegisterForm';

const page = () => {
	return <RegisterForm />;
};

page.layout = (page: any) => <AuthLayout children={page} />;
export default page;
