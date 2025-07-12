import { BarChart } from '@mantine/charts';
import { Card, Grid, Stack, Text } from '@mantine/core';
import { useMemo } from 'react';
import { ProductDto } from '../dto/products';
import { useFetchAnalyticReportQuery } from '../stores/actions/reports';
import LoadSkeleton from './UI/LoadSkeleton';

const AnalyticDashboard = () => {
	const { data, isFetching, isError, error } =
		useFetchAnalyticReportQuery(``);

	const categories = useMemo(() => {
		if (!data || !data?.categories || !data?.categories?.length) return [];
		return data.categories;
	}, [data]);

	if (isFetching || isError) {
		return (
			<LoadSkeleton
				isLoading={isFetching}
				isError={isError}
				error={error}
			/>
		);
	}

	return (
		<Grid>
			<Grid.Col span={{ lg: 6, sm: 6, xs: 12 }}>
				<Text size="lg" fw={600} mb="xl">
					Number of products in each category
				</Text>
				<BarChart
					h={300}
					data={categories}
					dataKey="name"
					series={[{ name: 'product_count' }]}
				/>
			</Grid.Col>
			<Grid.Col span={{ lg: 6, sm: 6, xs: 12 }}>
				<Stack>
					<Text size="lg" fw={600}>
						Recently added products
					</Text>

					<Grid>
						{data.recent_products.map(
							(item: ProductDto, i: number) => (
								<Grid.Col span={{ lg: 6, xs: 12 }}>
									<Card key={i} p="xs" withBorder shadow="xs">
										<Text size="sm">
											<span className="font-semibold">
												Name:{' '}
											</span>{' '}
											{item?.name ?? ''}
										</Text>
										<Text size="sm">
											<span className="font-semibold">
												Barcode:{' '}
											</span>{' '}
											{item?.barcode ?? ''}
										</Text>
										<Text size="sm">
											<span className="font-semibold">
												Category:{' '}
											</span>{' '}
											{item?.category?.name ?? ''}
										</Text>
									</Card>
								</Grid.Col>
							),
						)}
					</Grid>
				</Stack>
			</Grid.Col>
		</Grid>
	);
};

export default AnalyticDashboard;
